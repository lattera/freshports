<?
	# $Id: about.php,v 1.1.2.7 2002-05-22 04:30:19 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start("About this site",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

?>
<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><td VALIGN=TOP>
<TABLE>
<TR>
	<? freshports_PageBannerText("About this site"); ?>
</TR>

<TR><TD>
<P>
We have here a few notes about this website.
</P>
</TD></TR>

<TR>
<td valign="top" width="100%">

<TR>
	<? 
	freshports_BannerSpace();
	freshports_PageBannerText("What is a port?"); 
	?>
</TR>

<TR><TD>

<P>A port is the term used to describe a collection of files which makes it extremely
easy to install an application.  As it says in the <A HREF="http://www.freebsd.org/ports/">
FreeBSD Ports description</A>: <em>Installing an application is as simple as downloading 
the port, unpacking it and typing <b>make</b> in the port directory</em>. If you want an application, 
the port is the Way To Go(TM)</P>

<P>So off you go to the ports tree to install your favourite port.  It's quite easy. It's simple.
And you love that new application.  And you want to know when the port is updated.  That's where
we come in.</P>

<P>For more information about the Ports tree, see <A HREF="http://www.freebsd.org/ports/">http://www.freebsd.org/ports/</A>.</P>

</TD></TR>
<TR>
	<?
	freshports_BannerSpace();
	freshports_PageBannerText("What is $FreshPortsTitle");
	?>
</TR>

<TR><TD>

<P><? echo $FreshPortsTitle; ?> lists the change made to the ports tree. If you wish, <? echo $FreshPortsTitle; ?> can email you 
when your favourite port has been updated.
</P>

<P>
<? echo $FreshPortsTitle; ?> is not the place to report errors or request changes.  You should do that on the 
<A HREF="<? GLOBAL $mailto; echo $mailto; ?>:freebsd-ports&#64;freebsd.org">FreeBSD Ports mailing list</A>.  We do not maintain ports.  We do not
create ports.  We do not fix ports.  We just tell you what others have been doing to the Ports tree.
</P>

</TD></TR>


<TR>
	<?
	freshports_BannerSpace();
	freshports_PageBannerText("OK, whose bright idea was this?");
	?>
</TR>

<TR><TD>
<P>This site was created by Dan Langille.  His other web feats include 
<A HREF="http://www.freebsddiary.org/">The FreeBSD Diary</A>, <a 
href="http://www.racingsystem.com">The Racing System</A>, and an ability
to avoid reading the inane comments on <A HREF="http://slashdot.org">slashdot</A>.
But Dan didn't create the site all by himself.  Have a look at <A HREF="authors.php">
About the Authors</A> for details of who else helped.</P>
</TD></TR>

</TABLE>
</TD>
  <TD ALIGN="top">
    <?
       $ShowPoweredBy = 1;
       include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php");
    ?>
 </TD>
</TABLE>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</BODY>
</HTML>
