<?
	# $Id: pkg_utils.inc,v 1.5 2002-02-11 03:23:12 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

$Debug=0;

function dbgonly($s) {
	global $gDBG;
	return ($gDBG) ? $s : '';
}
//function pp($r) {
//    return print_r($r);
//}
//function ppp($r) {
//    return  dbgonly("<pre>DBG: " . pp($r) . "</pre>");
//}
function eppp($r) {
	global $gDBG;
	if ($gDBG) {
		print_r($r);
	}
}
function epp($s) {
	echo dbgonly("<pre>DBG: $s</pre>");
}

// {{{ function &DBConnect()
/**
*
* @param
* @return
*
*/
function &DBConnect() {
	global $db;

	return $db;
}
// }}}
// {{{ function Cleanup()
function Cleanup() {
}
//}}}
// {{{ function IsLoginValid($user,$pw,&$retval)
/**
*
* @param
* @return
*
*/
function IsLoginValid($user,$pw,&$retval) {
	$retval = false;
	$query = "select IsLoginValid('$user','$pw')";
	$database =& DBConnect();

	if ($database) {
		$result = pg_exec($database,$query);

		if ($result && pg_numrows($result)) {
			$row = pg_fetch_array($result,0);
			$retval = $row[0];
		}
	}
	else {
		echo "Error:  No Connection";
	}
	return $retval;
}
// }}}
// {{{ function GetWatchID($userID, &$watchID)
/**
*
* @param
* @return
*
*/
function GetWatchID($userID, &$watchID) {
	$retval   = false;
	$query    = "select id from watch_list where user_id = $userID";
	$database =& DBConnect();

	if ($database) {
		$result = pg_exec($database, $query);
	
		if ($result && pg_numrows($result)) {
			$retval  = true;
			$row     = pg_fetch_array($result,0);
			$watchID = $row[0];
		} else {
			# let's assume they have no watch list id yet.  so we'll create one for them
			#
			GLOBAL $Sequence_Watch_List_ID;

			$NextValue = freshports_GetNextValue($Sequence_Watch_List_ID, $database);
			$query     = "insert into watch_list (id, user_id, name) values ($NextValue, $userID, 'main')";
			$result    = pg_exec($database, $query);

/*
if ($result) {
	echo "got a result";
} else {
	echo "no result";
}

echo '<BR>';

echo 'rows affected ' . pg_cmdtuples($result) . '<BR>';
*/
			if ($result && pg_cmdtuples($result)) {
				$watchID = $NextValue;
				$retval  = true;
			} else {
				pg_errormessage($database) . " sql = $query";
			}
		}
	}
	else {
		echo "Error:  No Connection";
	}

#	echo "GetWatchID return value = '$retval'<BR>";

	return $retval;
}
// }}}
// {{{ function CleanPorts($watchID)
/**
*
* @param
* @return
*
*/
function CleanPorts($watchID) {
	$retval = true;
	$query = "delete from watch_list_element where watch_list_id = $watchID";
	$database =& DBConnect();

	echo "Cleaning out old ports<BR>\n";
	if ($database) {
		$result = pg_exec($database, $query);

		if (!$result) {
			$error = pg_errormessage($database);
			echo "$error<BR>\n";
			$retval = false;
		}
	}
	else {
		echo "Error:  No Connection";
		$retval = false;
	}
	return $retval;
}
// }}}
// {{{ function AddPortsToList($ports,$userID)
/**
*
* @param
* @return
*
*/
function AddPortsToList($ports, $userID) {
	$source = "AddPortsToList";
	$successful = 0;
	$dups = 0;
	// Connect and add the ports
	$database =& DBConnect();
	if ($database) {
		
		if (!$userID) {
			echo "Missing userID in $source";
			return false;
		}
		// optimize this
		foreach ($ports as $port) {
			$query = "select WatchListAdd($userID, '$port')";
			$result = @pg_exec($database, $query);

			if (!$result) {
				$errorMsg = pg_errormessage($database);
				if (strpos($errorMsg, 'Cannot insert a duplicate key')) {
					$dups++;
				}
				else {
					$errorLog[$port] = $errorMsg;
				}
			}
			else {
				$successful++;
			}
		}
	} 
	else {
		echo "Error:  No Connection";
	}

	echo "<P>Your pkg_info data has been uploaded and your watch list has been updated as follows:</P>";
	echo "Added $successful new ports<BR>\n";
	if ($dups) {
		echo "$dups ports in your update were already on your watch list.<BR>\n";
	}
	if (count($errorLog) > 0) {
		epp("<pre>");
		epp("ERROR LOG");
		foreach ($errorLog as $error) {
			echo $error . "<BR>\n";
		}
#		print_r($errorLog);
   		epp("</pre>");
	}
	if (count($errorLog) > 0) {
		return false;
	}
	return true;
}
// }}}
?>
