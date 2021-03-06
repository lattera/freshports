<?php
	#
	# $Id: categories.php,v 1.1.2.19 2003-03-06 22:09:32 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/databaselogin.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/user_tasks.php');


	freshports_Start('Categories',
					'freshports - new ports, applications',
					'FreeBSD, index, applications, ports');
					
	$Debug = 0;

	$AllowedToEdit = $User->IsTaskAllowed(FRESHPORTS_TASKS_CATEGORY_VIRTUAL_DESCRIPTION_SET);

	if ($AllowedToEdit) {
		$ColSpan = 5;
	} else {
   	$ColSpan = 4;
	}

?>

<table width="<? echo $TableWidth ?>" border="0" ALIGN="center">
<tr><td valign="top" width="100%">
<table width="100%" border="0" CELLPADDING="5">
  <tr>
	<? echo freshports_PageBannerText("$FreshPortsTitle - list of categories", $ColSpan); ?>
  </tr>
<tr><td COLSPAN="<?php echo $ColSpan; ?>">
<P>
This page lists the categories sorted by various categories.
</P>

<P>
You can sort each column by clicking on the header.  e.g. click on <b>Category</b> to sort by category.
</P>

</td></tr>
<script language="php">

$DESC_URL = 'ftp://ftp.freebsd.org/pub/FreeBSD/branches/-current/ports';


// make sure the value for $sort is valid

//echo "sort is $sort\n";

$sort				= AddSlashes($_GET['sort']);
$category		= AddSlashes($_GET['category']);
$count			= AddSlashes($_GET['count']);
$description	= AddSlashes($_GET['description']);

switch ($sort) {
   case 'category':
   case 'count':
   case 'description':
      $sort = $sort;
      $cache_file .= ".$sort";
      break;

   case 'lastupdate':
      $sort ='updated_raw desc';
      $cache_file .= '.updated';
      break;

   default:
      $sort = 'category';
      $cache_file .= '.category';
}

$sql = "
  SELECT to_char(max(commit_log.commit_date) - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as updated,
         count(ports.id) as count,
         max(commit_log.commit_date) - SystemTimeAdjust() as updated_raw,
         categories.id as category_id,
         categories.name as category,
         categories.description as description 
    FROM ports_categories, categories, element, ports left outer join commit_log on ( ports.last_commit_id = commit_log.id )
   WHERE ports.id             = ports_categories.port_id
     AND categories.id        = ports_categories.category_id
     AND ports.element_id     = element.id 
     AND element.status       = 'A'
GROUP BY categories.id, categories.name, categories.description ";

$sql .=  " ORDER BY $sort";

if ($Debug) echo '<pre>' . $sql, "</pre>\n";
//echo $sort, "\n";

$result = pg_exec($db, $sql);

$HTML .= freshports_echo_HTML('<tr>');

if ($sort == "category") {
   $HTML .= freshports_echo_HTML('<td><b>Category</b></td>');
} else {
   $HTML .= freshports_echo_HTML('<td><a href="categories.php?sort=category"><b>Category<b></a></td>');
}


if ($AllowedToEdit) {
	$HTML .= freshports_echo_HTML('<td><b>Action</b></td>');
}
	

if ($sort == "count") {
   $HTML .= freshports_echo_HTML('<td align="center"><b>Count</b></td>');
} else {
   $HTML .= freshports_echo_HTML('<td><a href="categories.php?sort=count"><b>Count</b></a></td>');
}

if ($sort == "description") {
   $HTML .= freshports_echo_HTML('<td><b>Description</b></td>');
} else {
   $HTML .= freshports_echo_HTML('<td><a href="categories.php?sort=description"><b>Description</b></a></td>');
}

if ($sort == "updated desc") {
   $HTML .= freshports_echo_HTML('<td nowrap><b>Last Update</b></td>');
} else {
   $HTML .= freshports_echo_HTML('<td nowrap><a href="categories.php?sort=lastupdate"><b>Last Update</b></a></td>');
}

$HTML .= freshports_echo_HTML('</tr>');

if (!$result) {
   print pg_errormessage() . "<br>\n";
   exit;
} else {
	$NumTopics	= 0;
	$NumPorts	= 0;
	$i			= 0;
	$NumRows = pg_numrows($result);
	while ($myrow = pg_fetch_array($result, $i)) {
		$HTML .= freshports_echo_HTML('<tr>');
		$HTML .= freshports_echo_HTML('<td valign="top"><a href="/' . $myrow["category"] . '/">' . $myrow["category"] . '</a></td>');

		if ($AllowedToEdit) {
			$HTML .= freshports_echo_HTML('<td valign="top"><a href="/category-maintenance.php?category=' . $myrow["category"] . '">update</a></td>');
		}

		$HTML .= freshports_echo_HTML('<td valign="top" ALIGN="right">' . $myrow["count"] . '</td>');
		$HTML .= freshports_echo_HTML('<td valign="top">' . $myrow["description"] . '</td>');
		$HTML .= freshports_echo_HTML('<td valign="top" nowrap><font size="-1">' . $myrow["updated"] . '</font></td>');
		$HTML .= freshports_echo_HTML("</tr>\n");


		$NumPorts += $myrow["count"];
		$i++;
		if ($i >  $NumRows - 1) {
			break;
		}
	}
}

$HTML .= freshports_echo_HTML('<tr><td><b>port count:</b></td>');
if ($AllowedToEdit) {
	$HTML .= freshports_echo_HTML('<td>&nbsp;</td>');
}
$HTML .= freshports_echo_HTML("<td ALIGN=\"right\"><b>$NumPorts</b></td><td>($NumRows categories)</td><td>&nbsp;</td></tr>");

$HTML .= freshports_echo_HTML('</table>');

freshports_echo_HTML_flush();

echo $HTML;                                                   

</script>
</td>

	<?
	freshports_SideBar();
	?>

</tr>
</table>

<?
freshports_ShowFooter();
?>

</body>
</html>
