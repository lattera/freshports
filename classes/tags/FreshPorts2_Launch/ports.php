<?
	# $Id: ports.php,v 1.1.2.19 2002-05-21 14:20:55 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited
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

	var $onwatchlist;	// 0 or 1 if set. not actually fetched directly by this classe.
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

	function FetchByPartialName($pathname, $WatchListID=0) {

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

		if ($Debug) echo "into FetchByPartialName with $pathname<BR>";

		if (IsSet($element->id)) {
			$this->element_id = $element->id;

			$sql = "select ports.id, ports.element_id, ports.category_id as category_id, " .
			       "ports.short_description as short_description, ports.long_description, ports.version as version, ".
			       "ports.revision as revision, ports.maintainer, ".
			       "ports.homepage, ports.master_sites, ports.extract_suffix, ports.package_exists, " .
			       "ports.depends_build, ports.depends_run, ports.last_commit_id, ports.found_in_index, " .
			       "ports.forbidden, ports.broken, to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, " .
			       "ports.categories as categories, ".
				   "element.name as port, categories.name as category," .
				   "element.status ";

			if ($WatchListID) {
				$sql .= ",
			       CASE when watch_list_element.element_id is null
		    	      then 0
		        	  else 1
			       END as onwatchlist ";
			}


			$sql .="from categories, element, ports ";

			#
			# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
			#
			if ($WatchListID) {
				$sql .="
			            left outer join watch_list_element
						on (ports.element_id                 = watch_list_element.element_id 
					   and  watch_list_element.watch_list_id = $WatchListID) ";
			}

			$sql .="WHERE ports.element_id     = $this->element_id ".
			       "  and ports.category_id    = categories.id " .
			       "  and ports.element_id     = element.id ";


			if ($Debug) {
				echo $sql;
				exit;
			}

	        $result = pg_exec($this->dbh, $sql);
			if ($result) {
				$numrows = pg_numrows($result);
				if ($numrows == 1) {
					if ($Debug) echo "fetched by ID succeeded<BR>";
					$myrow = pg_fetch_array ($result, 0);
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

	function FetchByName($PortName, $WatchListID=0) {

		$Debug = 0;

		$numrows = 0; 	# nothing found

		# fetch zero or more ports based on the name.
		# e.g. samba, logcheck
		# it returns the number of ports found.  You must call FetchNth
		#
		# first, we get the element relating to this port
		#
		$sql = "select ports.id, ports.element_id, ports.category_id as category_id, " .
		       "ports.short_description as short_description, ports.long_description, ports.version as version, ".
		       "ports.revision as revision, ports.maintainer, ".
		       "ports.homepage, ports.master_sites, ports.extract_suffix, ports.package_exists, " .
		       "ports.depends_build, ports.depends_run, ports.last_commit_id, ports.found_in_index, " .
		       "ports.forbidden, ports.broken, to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, " .
		       "ports.categories as categories, ".
			   "element.name as port, categories.name as category," .
			   "element.status ";

		if ($WatchListID) {
			$sql .= ",
		       CASE when watch_list_element.element_id is null
	    	      then 0
	        	  else 1
		       END as onwatchlist ";
		}


		$sql .="from categories, element, ports ";

		#
		# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
		#
		if ($WatchListID) {
			$sql .="
		            left outer join watch_list_element
					on (ports.element_id                 = watch_list_element.element_id 
				   and  watch_list_element.watch_list_id = $WatchListID) ";
		}

		$sql .="WHERE ports.category_id    = categories.id " .
		       "  and ports.element_id     = element.id ".
			   "  and element.name         = $PortName";


		if ($Debug) {
			echo $sql;
			exit;
		}

        $this->LocalResult = pg_exec($this->dbh, $sql);
		if ($this->LocalResult) {
			$numrows = pg_numrows($this->LocalResult);
		} else {
			echo 'pg_exec failed: ' . $sql;
		}

		return $numrows;
	}

	function FetchByID($id) {
		# fetch a single port based on id
		# I don't think this is actually used.

		echo "classes/ports.php::FetchByID has been invoked. Who called it?<BR>";
		exit;

		$sql = "select ports.id, ports.element_id, ports.category_id as category_id, " .
		       "ports.short_description as short_description, ports.long_description, ports.version as version, ".
		       "ports.revision as revision, ports.maintainer, ".
		       "ports.homepage, ports.master_sites, ports.extract_suffix, ports.package_exists, " .
		       "ports.depends_build, ports.depends_run, ports.last_commit_id, ports.found_in_index, " .
		       "ports.forbidden, ports.broken, to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, " .
		       "ports.categories as categories, ".
			   "element.name as port, categories.name as category, commit_log_ports.needs_refresh, " .
			   "element.status, to_char(max(commit_log.commit_date) - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as updated ";

		if ($WatchListID) {
			$sql .= ",
		       CASE when watch_list_element.element_id is null
		          then 0
		          else 1
		       END as onwatchlist ";
		}


		$sql .=" from categories, element, commit_log_ports, commit_log, ports ";

		#
		# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
		#
		if ($WatchListID) {
			$sql .="
		            left outer join watch_list_element
					on watch_list_element.element_id    = ports.element_id 
				   and watch_list_element.watch_list_id = $WatchListID ";
		}

		$sql .= "WHERE ports.id             = $id ".
		        "  and ports.category_id    = categories.id " .
		        "  and ports.element_id     = element.id " .
			    "  and ports.last_commit_id = commit_log_ports.commit_log_id " .
			    "  and ports.id             = commit_log_ports.port_id " .
			    "  and commit_log.id        = commit_log_ports.commit_log_id ";

        $result = pg_exec($this->dbh, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
			if ($numrows == 1) {
				if ($Debug) echo "fetched by ID succeeded<BR>";
				$myrow = pg_fetch_array ($result, 0);
				$this->_PopulateValues($myrow);

				#
				# I had considered including an OUTER JOIN in the above SQL
				# but didn't.  I figured the above was
				if ($WatchListID) {
					$this->onwatchlist = IsOnWatchList($WatchListID);
				}

			}
		} else {
			echo 'pg_exec failed: ' . $sql;
		}
	}

	function FetchByCategoryInitialise($CategoryID, $WatchListID=0) {
		# fetch all ports based on category
		# e.g. id for net
		$sql = "select ports.id, ports.element_id, ports.category_id as category_id, " .
		       "       ports.short_description as short_description, ports.long_description, ports.version as version, ".
		       "       ports.revision as revision, ports.maintainer, ".
		       "       ports.homepage, ports.master_sites, ports.extract_suffix, ports.package_exists, " .
		       "       ports.depends_build, ports.depends_run, ports.last_commit_id, ports.found_in_index, " .
		       "       ports.forbidden, ports.broken, to_char(max(ports.date_added) - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, " .
		       "       ports.categories as categories, ".
			   "       element.name as port, categories.name as category, " .
			   "       element.status ";

		if ($WatchListID) {
			$sql .= ",
		       CASE when watch_list_element.element_id is null
		          then 0
		          else 1
		       END as onwatchlist ";
		}


		$sql .=" from categories, element, ports ";

		#
		# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
		#
		if ($WatchListID) {
			$sql .="
		            left outer join watch_list_element
					on watch_list_element.element_id    = ports.element_id 
				   and watch_list_element.watch_list_id = $WatchListID ";
		}

		$sql .= "WHERE ports.category_id    = categories.id " .
		        "  and ports.element_id     = element.id " .
				"  and categories.id        = $CategoryID " .
				"  and element.status       = 'A' " .
				"GROUP BY ports.id, ports.element_id, category_id, short_description, ports.long_description,
				          version, revision, ports.maintainer, ports.homepage, ports.master_sites, ports.extract_suffix, 
						  ports.package_exists, ports.depends_build, ports.depends_run, ports.last_commit_id, 
						  ports.found_in_index, ports.forbidden, ports.broken, ports.date_added, 
						  categories, port, category, element.status ";

		if ($WatchListID) {
			$sql .= ", watch_list_element.element_id";
		}

		$sql .= " ORDER by port ";

        $this->LocalResult = pg_exec($this->dbh, $sql);
		if ($this->LocalResult) {
			$numrows = pg_numrows($this->LocalResult);
			if ($numrows == 1) {
#				echo "fetched by ID succeeded<BR>";
				$myrow = pg_fetch_array ($this->LocalResult, 0);
				$this->_PopulateValues($myrow);

			}
		} else {
			echo 'pg_exec failed: ' . $sql . ' : ' . pg_errormessage();
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
}
