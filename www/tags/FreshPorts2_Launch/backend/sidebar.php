<?
	# $Id: sidebar.php,v 1.1.2.8 2002-05-26 05:06:08 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_HTML_Start();

?>

<HEAD>
	<TITLE>FreshPorts</TITLE>

	<STYLE TYPE="text/css">
	BODY, TD, TR, P, UL, OL, LI, INPUT, SELECT, DL, DD, DT, FONT
	{
		font-family: Helvetica, Verdana, Arial, Clean, sans-serif;
		font-size: 12px;
	}
	ul { padding-left: 20px;}
	</STYLE>

	<META HTTP-EQUIV="Refresh" CONTENT="1200; URL=http://test.FreshPorts.org/sidebar.php">
	<META http-equiv="Pragma"              content="no-cache">
	<META NAME="MSSmartTagsPreventParsing" content="TRUE">

</HEAD>

<?
	freshports_body();
	$ServerName = str_replace("freshports", "FreshPorts", $_SERVER["SERVER_NAME"]);
?>

	<CENTER>
	<A HREF="http://<? echo $ServerName; ?>/" TARGET="_content"><IMG SRC="/images/freshports_mini.jpg" ALT="FreshPorts.org - the place for ports" WIDTH="128" HEIGHT="28" BORDER="0"></A>

	<BR>

	<SMALL>
	<script LANGUAGE="JavaScript">
		var d = new Date();  // today's date and time.
	    document.write(d.toLocaleString());
	</script>
	</SMALL>
	</CENTER>

<?

	$sql = "select * from commits_latest order by commit_date_raw desc, category, port";

	$sql .= " limit 40";

	if ($Debug) {
		echo $sql;
		}

	$result = pg_exec ($db, $sql);
	if (!$result) {
		echo $sql . 'error = ' . pg_errormessage();
		exit;
	}
?>

	<UL>
<?
	$numrows = pg_numrows($result);
	for ($i = 0; $i < $numrows; $i++) {
		$myrow = pg_fetch_array ($result, $i);
		echo '	<LI><SMALL><A HREF="http://' . $ServerName . '/' . $myrow["category"] . '/' . $myrow["port"] . '/" TARGET="_content">';
		echo $myrow["category"] . '/' . $myrow["port"] . '</A>';
		echo '</SMALL></LI>';
		echo "\n";
	}
?>
	</UL>

<P ALIGN="right">
<? echo freshports_copyright(); ?>
</P>

</body>
</html>
