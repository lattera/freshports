<?
	# $Id: authors.php,v 1.1.2.8 2002-05-22 04:30:19 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start("The Authors",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

?>
<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<tr><td valign="top">
<table width="100%" border="0">
  <tr>
	<? freshports_PageBannerText("About the authors"); ?>
  </tr>
<TR><TD>
<p>Dan Langille thought up the idea, found the data sources, bugged people to 
write scripts, and did the html and database work. But he certainly didn't 
do it alone.</p>

<H2>FreshPorts 2</H2>

<P>I apologize as I have not been keeping this list up to date and therefore I fear I have
   missed people but I don't know who.  Please let me know if you should be here.</P>

<UL>
<LI>Marcin Gryszkalis did the underlying work for the the <A HREF="/graphs.php">graphs</A>.  He
    also helped out with the htmlifying of the log message (so you can click on PR and email and URLs).</LI>

<LI>Jonathan Sage helped to reclaim some missing ports by writing some perl code to pull
    things out of CVS.</LI>

<LI>Dan Peterson showed me the wonders of <A HREF="http://cr.yp.to/daemontools.html">Daemon Tools</A>
    which handles the processing of incoming messages and refreshes the main web page.</A></LI>

<LI>Josef Karthauser for helping me through the cvs-all log format and for greatly simplifying the
	job of FP2.</LI>

<LI>Titus Manea again always has great ideas.  His knowledge base of *nix far exceeds my own.</LI>

<LI>Ade Lovett for grilling me about my need for daemons and leading me to discover Daemon Tools via Dan P.
	And for his mega-commits which prompted me to show abbreviated commits.</LI>

</UL>


<H2>FreshPorts (original)</H2>

<UL>

<LI>Olaf wrote did the perl script for the log catcher.</LI>

<LI>icmpecho wrote the awk code for the log catcher and the log munger.</LI>

<LI>Adriel helped me with perl syntax.</LI>

<LI>Acme talked over data sources with me.</LI>

<LI>John Polstra and Satoshi Asami provided insight into cvs and ports as well
as encouragement.</LI>

<LI>Laz hung around, criticized, and suggested security improvements.</LI>

<LI>halflife did some prototype coding for me.</LI>

<LI>David Bushong did a FreshBSD site which is a freshmeat-look site.</LI>

<LI>lzh on undernet #perl helped me with my perl knowledge.  Some of his examples 
form the basis for some of the most important parts of the system.  Aquitaine
also showed me the PERL dbi->quote() function.</LI>

<LI>John Beige did the logo you see at the top of the page.</LI>

<LI>Wolfram Schneider's <a href="http://www.freebsd.org/cgi/ports.cgi">FreeBSD Ports Changes</a>
page provided much of the basis for this site.</LI>
	
<LI>Jay gave me the box on which FreshPorts runs.  Thanks.</LI>

<LI>And various people on undernet's #nz.general and #freebsd helped me with 
scripts and ideas.  That's not to mention that channel on efnet which I won't 
name just so it stays a secret.</LI>

</UL>

</TD>
</TR>
</TABLE>
</td>
  <td valign="top" width="*">
    <? include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
 </td>
</tr>
</table>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
