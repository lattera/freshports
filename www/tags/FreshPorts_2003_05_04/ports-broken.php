<?php
	#
	# $Id: ports-broken.php,v 1.1.2.14 2003-04-28 16:25:14 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/databaselogin.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');

	$Debug = 0;

	$Title = 'Broken ports';

	freshports_Start($Title,
					'freshports - new ports, applications',
					'FreeBSD, index, applications, ports');

?>

<TABLE width="<? echo $TableWidth ?>" border="0" ALIGN="center">

<TR><TD valign="top" width="100%">
<TABLE width="100%" border="0">
<TR>
	<? echo freshports_PageBannerText($Title); ?>
</TR>
<TR><TD>
These are the recently added ports.
</TD></TR>
<?

	$DESC_URL = 'ftp://ftp.freebsd.org/pub/FreeBSD/branches/-current/ports';

	$visitor = $_COOKIE["visitor"];
	$sort    = $_REQUEST["sort"];

	// make sure the value for $sort is valid

	echo "<TR><TD>\nThis page is ";

	switch ($sort) {
		case 'dateadded':
			$sort = 'ports.date_added desc, category, port';
			echo 'sorted by date added.  but you can sort by <a href="' . $_SERVER["PHP_SELF"] . '?sort=category">category</a>';
			$ShowCategoryHeaders = 0;
			break;

		default:
			$sort ='category, port';
			echo 'sorted by category.  but you can sort by <a href="' . $_SERVER["PHP_SELF"] . '?sort=dateadded">date added</a>';
			$ShowCategoryHeaders = 1;
	}

	echo "</TD></TR>\n";

	$sql = "
SELECT ports.id, 
       element.name    as port,  
       categories.name as category, 
       ports.category_id, 
       version         as version, 
       revision        as revision,
       ports.element_id,
       maintainer, 
       short_description, 
       to_char(ports.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added,
       last_commit_id  as last_change_log_id,
       package_exists,
       extract_suffix,
       homepage,
       status,
       broken,
       forbidden ";

	if ($User->id) {
		$sql .= ",
         onwatchlist";
   }

	$sql .= "
from element, categories, ports ";

	if ($User->id) {
			$sql .= "
      LEFT OUTER JOIN
 (SELECT element_id as wle_element_id, COUNT(watch_list_id) as onwatchlist
    FROM watch_list JOIN watch_list_element
        ON watch_list.id      = watch_list_element.watch_list_id
       AND watch_list.user_id = $User->id
       AND watch_list.in_service
  GROUP BY wle_element_id) AS TEMP
       ON TEMP.wle_element_id = ports.element_id";
	}
	

	$sql .= "
WHERE ports.element_id  = element.id
  and ports.category_id = categories.id 
  and status            = 'A' 
  and ports.broken      <> ''";

	$sql .= " order by $sort ";
#	$sql .= " limit 20";

	if ($Debug) {
		echo "<pre>$sql</pre>";
	}

	$result = pg_exec($db, $sql);
	if (!$result) {
		echo pg_errormessage();
	} else {
		$numrows = pg_numrows($result);
#		echo "There are $numrows to fetch<BR>\n";
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/list-of-ports.php');

	echo freshports_ListOfPorts($result, $db, 'Y', $ShowCategoryHeaders);
?>

</TABLE>

</TD>

  <TD VALIGN="top" WIDTH="*" ALIGN="center">
	<?
	freshports_SideBar();
	?>
  </td>

</TR>
</TABLE>

<?
freshports_ShowFooter();
?>

</body>
</html>

