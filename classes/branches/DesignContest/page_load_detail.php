<?php
	#
	# $Id: page_load_detail.php,v 1.2 2006-12-17 11:37:20 dan Exp $
	#
	# Copyright (c) 2003 DVL Software Limited
	#

	$Debug = 0;

// base class for keeping statistics on page rendering issues
class PageLoadDetail {

	var $dbh;

	var $StartTime;

	var $LocalResult;


	function PageLoadDetail() {
		$this->StartTime = microtime();
	}
	
	function DBSet($dbh) {
		$this->dbh	= $dbh;
	}
	
	function ElapsedTime() {
		#
		# function to return the absolute difference between two microtime strings.
		# as obtained from PHP user contributed notes
		# mdchaney@michaelchaney.com (19-Oct-2002 07:53)
		#

		list($a_micro, $a_int)=explode(' ', $this->StartTime);
		list($b_micro, $b_int)=explode(' ', microtime());
		if ($a_int > $b_int) {
			return ($a_int-$b_int)+($a_micro-$b_micro);
		} elseif ($a_int==$b_int) {
			if ($a_micro>$b_micro) {
				return ($a_int-$b_int)+($a_micro-$b_micro);
			} elseif ($a_micro<$b_micro) {
				return ($b_int-$a_int)+($b_micro-$a_micro);
			} else {
				return 0;
			}
		} else { // $a_int<$b_int
			return ($b_int-$a_int)+($b_micro-$a_micro);
		}
	}

	function Save() {
		#
		# Record the statistics
		#
		
		GLOBAL $User;

		$Debug = 0;
		
		$UserID = $User->id == '' ? "NULL" : $User->id;
#		echo "\$UserID='$UserID'<br>";

		$sql = "
INSERT INTO page_load_detail(page_name,
                             user_id,
                             ip_address,
                             full_url,
                             rendering_time)
                     values ('" . AddSlashes($_SERVER['SCRIPT_NAME']) . "',
                             $UserID,
                             '" . AddSlashes($_SERVER['REMOTE_ADDR']) . "',
                             '" . AddSlashes($_SERVER["REQUEST_URI"]) . "',
                             '" . $this->ElapsedTime() . " seconds')";
		if ($Debug) echo "CODE <pre>$sql</pre>";
		$result = pg_exec($this->dbh, $sql);
		if ($result) {
			$return = 1;
		} else {
			echo "error " . pg_errormessage();
			$return = -1;
		}

		return $return;
	}

}
