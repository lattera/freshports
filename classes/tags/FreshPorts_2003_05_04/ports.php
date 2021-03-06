<?php
	#
	# $Id: ports.php,v 1.1.2.32 2003-04-28 16:21:13 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#


// base class for Port
class Port {

	// set on new
	var $dbh;

	// from the ports table
	var $id;
	var $element_id;
	var $category_id;
	var $short_description;
	var $long_description;
	var $version;
	var $revision;
	var $maintainer;
	var $homepage;
	var $master_sites;
	var $extract_suffix;
	var $package_exists;
	var $depends_build;
	var $depends_run;
	var $last_commit_id;
	var $found_in_index;
	var $forbidden;
	var $broken;
	var $date_added;
	var $categories;

	// derived or from other tables
	var $category;
	var $port;
	var $needs_refresh;
	var $status;
	var $updated;	// timestamp of last update

	var $onwatchlist;	// count of how many watch lists is this port on for this user. 
							// not actually fetched directly by this class.
							// normally used only if you've specified it in your own SQL.

	// not always present/set
	var $update_description;

	// so far used by ports-deleted.php and include/list-of-ports.php
	var $message_id;
	var $encoding_losses;

	// needed for fetch by category
	var $LocalResult;

	var $committer; 

	function Port($dbh) {
		$this->dbh	= $dbh;
	}

	function _PopulateValues($myrow) {
		$this->id                 = $myrow["id"];
		$this->element_id         = $myrow["element_id"];
		$this->category_id        = $myrow["category_id"];
		$this->short_description  = $myrow["short_description"];
		$this->long_description   = $myrow["long_description"];
		$this->version            = $myrow["version"];
		$this->revision           = $myrow["revision"];
		$this->maintainer         = $myrow["maintainer"];
		$this->homepage           = $myrow["homepage"];
		$this->master_sites       = $myrow["master_sites"];
		$this->extract_suffix     = $myrow["extract_suffix"];
		$this->package_exists     = $myrow["package_exists"];
		$this->depends_build      = $myrow["depends_build"];
		$this->depends_run        = $myrow["depends_run"];
		$this->last_commit_id     = $myrow["last_commit_id"];
		$this->found_in_index     = $myrow["found_in_index"];
		$this->forbidden          = $myrow["forbidden"];
		$this->broken             = $myrow["broken"];
		$this->date_added         = $myrow["date_added"];
		$this->categories         = $myrow["categories"];

		$this->port               = $myrow["port"];
		$this->category           = $myrow["category"];
		$this->needs_refresh      = $myrow["needs_refresh"];
		$this->status             = $myrow["status"];
		$this->updated            = $myrow["updated"];

		$this->onwatchlist        = $myrow["onwatchlist"];

		$this->update_description = $myrow["update_description"];
		$this->message_id         = $myrow["message_id"];
		$this->encoding_losses    = $myrow["encoding_losses"];
		$this->committer          = $myrow["committer"];
	}

	function FetchByPartialName($pathname, $UserID = 0) {

		# I THINK THIS FUNCTION IS NOT REQUIRED.

		$Debug = 0;

		# fetch a single port based on pathname.
		# e.g. net/samba
		#
		# It will not bring back any commit information.

		#
		# first, we get the element relating to this port
		#
		$element = new Element($this->dbh);
      $element->FetchByName($pathname);
      
      # * * * * * * * * * * * * * * * * * * * * * * * * *
      # Now that we have the ID, we should call FetchByID!
      # * * * * * * * * * * * * * * * * * * * * * * * * *

		if ($Debug) echo "into FetchByPartialName with $pathname<BR>";

		if (IsSet($element->id)) {
			$this->element_id = $element->id;

			$sql = "
select ports.id,
       ports.element_id,
       ports.category_id       as category_id, 
       ports.short_description as short_description, 
       ports.long_description, 
       ports.version           as version, 
       ports.revision          as revision, 
       ports.maintainer, 
       ports.homepage, 
       ports.master_sites, 
       ports.extract_suffix, 
       ports.package_exists, 
       ports.depends_build, 
       ports.depends_run, 
       ports.last_commit_id, 
       ports.found_in_index, 
       ports.forbidden, 
       ports.broken, 
       to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, 
       ports.categories as categories,
	    element.name     as port, 
	    categories.name  as category,
	    element.status ";

			if ($UserID) {
				$sql .= ",
	        TEMP.onwatchlist";
	      }

	      $sql .= "
       from categories, element, ports ";

			if ($UserID) {
				$sql .= "
	      LEFT OUTER JOIN
	 (SELECT element_id as wle_element_id, COUNT(watch_list_id) as onwatchlist
	    FROM watch_list JOIN watch_list_element 
	        ON watch_list.id      = watch_list_element.watch_list_id
	       AND watch_list.user_id = $UserID
	  GROUP BY element_id) AS TEMP
	       ON TEMP.wle_element_id = ports.element_id";
	      }
	

			$sql .= " WHERE element.id        = $this->element_id 
			            and ports.category_id = categories.id 
			            and ports.element_id  = element.id ";


			if ($Debug) {
				echo "<pre>$sql</pre>";
			}

	      $result = pg_exec($this->dbh, $sql);
			if ($result) {
				$numrows = pg_numrows($result);
				if ($numrows == 1) {
					if ($Debug) echo "fetched by ID succeeded<BR>";
					$myrow = pg_fetch_array ($result);
					$this->_PopulateValues($myrow);
				} else {
					echo "Ports::FetchByPartialName I'm concerned I got $numrows from that.<BR>$sql<BR>";
				}
			} else {
				echo 'pg_exec failed: ' . $sql;
			}
		} else {
			echo 'ports FetchByPartialName for $path failed';
		}
	}

	function FetchByID($id, $UserID = 0) {
		# fetch a single port based on id
		# used by missing-port.php

		$Debug = 0;

		$sql = "select ports.id, 
		               ports.element_id, 
		               ports.category_id       as category_id,
		               ports.short_description as short_description, 
		               ports.long_description, 
		               ports.version           as version,
		               ports.revision          as revision, 
		               ports.maintainer,
		               ports.homepage, 
		               ports.master_sites, 
		               ports.extract_suffix, 
		               ports.package_exists,
		               ports.depends_build, 
		               ports.depends_run, 
		               ports.last_commit_id, 
		               ports.found_in_index,
		               ports.forbidden, 
		               ports.broken, 
		               to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added,
		               ports.categories as categories,
			            element.name     as port, 
			            categories.name  as category,
			            element.status ";

		if ($UserID) {
			$sql .= ', 
CASE WHEN TEMP.onwatchlist IS NULL
THEN 0 ELSE 1
END as onwatchlist';
		}


		$sql .= " from categories, element, ports ";

		#
		# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
		#
		if ($UserID) {
			$sql .="
LEFT OUTER JOIN (
SELECT element_id as wle_element_id, COUNT(watch_list_id) as onwatchlist
    FROM watch_list JOIN watch_list_element
        ON watch_list.id      = watch_list_element.watch_list_id
       AND watch_list.user_id = $UserID
       AND watch_list.in_service
  GROUP BY element_id
) AS TEMP
ON TEMP.wle_element_id = ports.element_id";

		}

		$sql .= "\nWHERE ports.id        = $id 
		          and ports.category_id = categories.id 
		          and ports.element_id  = element.id ";

		if ($Debug) {
			echo "<pre>$sql</pre>";
			exit;
		}

      $result = pg_exec($this->dbh, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
			if ($numrows == 1) {
				if ($Debug) echo "fetched by ID succeeded<BR>";
				$myrow = pg_fetch_array ($result);
				$this->_PopulateValues($myrow);

				#
				# I had considered including an OUTER JOIN in the above SQL
				# but didn't.  I figured the above was
				if ($WatchListID) {
					$this->onwatchlist = IsOnWatchList($WatchListID);
				}

			}
		} else {
			echo 'pg_exec failed: <pre>' . $sql . '</pre>';
		}
	}

	function FetchByCategoryInitialise($CategoryName, $UserID = 0, $PageSize = 0, $PageNo = 0) {
		# fetch all ports based on category
		# e.g. id for net
		
		$Debug = 0;

		$sql = "";
		if ($UserID) {
			$sql .= "SELECT PE.*,

CASE WHEN watchlistcount IS NULL
THEN 0 ELSE 1
END as onwatchlist

FROM
 (";
     	}

		$sql .= "
SELECT P.*, element.name    as port,
        element.status  as status
   FROM element JOIN
 (SELECT ports.id,
        ports.element_id        as element_id,
        ports.category_id       as category_id,
        ports.short_description as short_description,
        ports.long_description,
        ports.version           as version,
        ports.revision          as revision,
        ports.maintainer,
        ports.homepage,
        ports.master_sites,
        ports.extract_suffix,
        ports.package_exists,
        ports.depends_build,
        ports.depends_run,
        ports.last_commit_id,
        ports.found_in_index,
        ports.forbidden,
        ports.broken,
        to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added,
        ports.categories as categories,
        categories.name  as category
   FROM ports, categories, ports_categories
  WHERE ports_categories.port_id     = ports.id
    AND ports_categories.category_id = categories.id
    AND categories.name              = '$CategoryName' ) AS P
   ON (P.element_id     = element.id
   AND element.status   = 'A')";

		if ($UserID) {
			$sql .= ") AS PE
LEFT OUTER JOIN
 (SELECT element_id           as wle_element_id,
         COUNT(watch_list_id) as watchlistcount
    FROM watch_list JOIN watch_list_element
      ON watch_list.id      = watch_list_element.watch_list_id
     AND watch_list.user_id = $UserID
     AND watch_list.in_service
 GROUP BY wle_element_id) AS TEMP
  ON TEMP.wle_element_id = PE.element_id";
    	}

		$sql .= " ORDER by port ";
		
#echo "\$PageSize='$PageSize'\n";
		if ($PageSize) {
			$sql .= " LIMIT $PageSize";
			if ($PageNo) {
				$sql .= ' OFFSET ' . ($PageNo - 1 ) * $PageSize;
			}
		}

		if ($Debug) echo "<pre>$sql</pre>";

		$this->LocalResult = pg_exec($this->dbh, $sql);
		if ($this->LocalResult) {
			$numrows = pg_numrows($this->LocalResult);
			if ($numrows == 1) {
#				echo "fetched by ID succeeded<BR>";
				$myrow = pg_fetch_array ($this->LocalResult);
				$this->_PopulateValues($myrow);

			}
		} else {
			echo 'pg_exec failed: <pre>' . $sql . '</pre> : ' . pg_errormessage();
		}

		return $numrows;
	}

	function FetchNth($N) {
		#
		# call FetchByCategoryInitialise first.
		# then call this function N times, where N is the number
		# returned by FetchByCategoryInitialise
		#

		$myrow = pg_fetch_array($this->LocalResult, $N);
		$this->_PopulateValues($myrow);
	}

	function IsOnWatchList($WatchListID) {
		#
		# return non-zero if this port is on the supplied watch list ID.
		# zero otherwise.
		#

		$result = 0;

		$sql = "	select element_id
					  from watch_list_element
					 where watch_list_id = $WatchListID
					   and element_id    = $this->element_id";

		$result = pg_exec($this->dbh, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
			if ($numrows == 1) {
				if ($Debug) echo "IsOnWatchList succeeded<BR>";
				$result = 1;
			}
		} else {
			echo 'pg_exec failed: ' . $sql;
		}

		return $result;
	}

	function Fetch($Category, $Port, $UserID = 0) {
		#
		# introduced for virtual categories.
		# given a category port combination, let's get the port id
		# and then fetch
		#

		$sql = "select GetPortID('" . AddSlashes($Category) . "', '"  . AddSlashes($Port) . "') as port_id";
		$result = pg_exec($this->dbh, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
			if ($numrows == 1) {
				$myrow = pg_fetch_row($result);
				$PortID = $myrow[0];
				if (IsSet($PortID)) {
					$result = $this->FetchByID($myrow[0], $UserID);
				} else {
					return $PortID;
				}
			} else {
				echo 'that port was not found:' . $Category . '/' . $Port;
			}
		} else {
			echo 'pg_exec failed: ' . $sql;
		}

		return $result;
	}
}
