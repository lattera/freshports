<?
	# $Id: confirmation.php,v 1.1.2.1 2002-02-24 00:21:11 dan Exp $
	#
	# Copyright (c) 1998-2002 DVL Software Limited

    require("./include/common.php");
    require("./include/freshports.php");
    require("./include/databaselogin.php");

    require("./include/getvalues.php");

	freshports_Start("Account confirmation",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");
	$Debug = 0;

	$ResultConfirm = 999;

	if (IsSet($token)) {
		$token = AddSlashes($token);
		if ($Debug) echo "I'm confirming with token $token\n<BR>";
		$sql = "select ConfirmUserAccount('$token')";
		$result = pg_exec($db, $sql);
		if ($result) {
			$row = pg_fetch_array($result,0);
			$ResultConfirm = $row[0];
		} else {
			echo pg_errormessage() . $sql;
		}
	}
?>


<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<tr><td VALIGN=TOP>
<TABLE WIDTH="100%">
<TR>
	<? freshports_PageBannerText("Account confirmation"); ?>
</TR>

<TR><TD>
<P>
<?
	if ($Debug) echo $ResultConfirm;
	switch ($ResultConfirm) {
		case 0:
			echo "I don't know anything about that token.";
			break;

		case 1:
			echo 'Your account has been enabled.  Please proceed to the <A HREF="login.php">login page</A>';
			break;

		case 2:
			echo "Well.  This just isn't supposed to happen.  For some strange and very rare reason,
				 there is more than one person with that token.<BR><BR>Please contact webmaster&#64;freshports.org for help.";
			break;

		case -1:
			echo "An error has occurred.  Sorry.";
			break;

		case 999:
			echo "Hi there.  What you are doing here?";
			break;

		default:
	}

?>
</P>
</TD></TR>

</table>
</td>
  <td valign="top" width="*">
    <?
       include("./include/side-bars.php");
    ?>
 </td>
</tr>
</table>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? include("./include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
