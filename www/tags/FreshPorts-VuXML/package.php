<?php
	#
	# $Id: package.php,v 1.1.2.2 2004-11-17 22:37:27 dan Exp $
	#
	# Copyright (c) 2004 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/databaselogin.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/searches.php');

	$Title = 'Search by package';

	freshports_Start("$Title",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

	if (IsSet($_REQUEST['notfound'])) $notfound = 1;
	if (IsSet($_REQUEST['multiple'])) $multiple = 1;

	$package  = AddSlashes($_REQUEST['package']);

	$Searches = new Searches($dbh);

?>
<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><td VALIGN=TOP width="100%">
<TABLE WIDTH="100%" ALIGN="left" border="0">
<TR>
	<? echo freshports_PageBannerText($Title); ?>
</TR>

<TR><TD>
<P>

<?php
if ($notfound) {
?>
The package specified ('<?php echo $package; ?>') could not be found.  We have a few suggestions.
<ul>
<li><a href="<?php echo $Searches->GetDefaultSearchString($package); ?>">Search</a> for ports containing '<?php echo $package; ?>' in their name.
<li><a href="<?php echo $Searches->GetDefaultSoundsLikeString($package); ?>">Search</a> for ports which sound like '<?php echo $package; ?>'.
</ul>
<?php
} else {
	die('I have no idea what I should be doing');
}
?>
</P>
</TD></TR>

</TABLE>
</TD>

  <TD VALIGN="top" WIDTH="*" ALIGN="center">
	<?
	freshports_SideBar();
	?>
  </td>

</TABLE>

<?php
	GLOBAL $ShowPoweredBy;
	$ShowPoweredBy = 1;
?>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? freshports_ShowFooter(); ?>
</TD></TR>
</TABLE>

</BODY>
</HTML>
