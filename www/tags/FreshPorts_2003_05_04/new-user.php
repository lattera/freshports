<?php
	#
	# $Id: new-user.php,v 1.1.2.32 2003-03-20 02:22:20 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/databaselogin.php');

	$origin = $_GET['origin'];
	$submit = $_POST['submit'];

if ($origin == '/index.php' || $origin == '') {
	$origin = '/';
}

if ($submit) {

	// process form

	/*
	while (list($name, $value) = each($HTTP_POST_VARS)) {
		echo "$name = $value<BR>\n";
	}
	*/


	$OK = 1;

	$errors = "";

	$UserLogin				= AddSlashes($_POST["UserLogin"]);
	$email					= AddSlashes($_POST["email"]);
	$Password1				= AddSlashes($_POST["Password1"]);
	$Password2				= AddSlashes($_POST["Password2"]);
	$numberofdays			= AddSlashes($_POST["numberofdays"]);

	if ($UserLogin == '') {
		$errors .= "Please enter a user id.<BR>";
		$OK = 0;
	}

	if (!freshports_IsEmailValid($email)) {
		$errors .= "That email address doesn't look right to me<BR>";
		$OK = 0;
	}

	if ($Password1 != $Password2) {
		$errors .= "The password was not confirmed.  It must be entered twice.<BR>";
		$OK = 0;
	} else {
		if ($Password1 == '') {
			$errors .= 'A password must be supplied<BR>';
			$OK = 0;
		}
	}

	#
	# make sure we have valid values in this variable.
	# by default, they don't get notified.
	#

	$UserCreated = 0;
	if ($OK) {
		$Cookie = UserToCookie($UserLogin);
//		echo "checking database\n";

		// test for existance of user id

		$sql = "select * from users where cookie = '$Cookie'";

		$result = pg_exec($db, $sql) or die('query failed');

		// create user id if not found
		if(!pg_numrows($result)) {
 //			echo "confirmed: user id is new\n";

			$UserID = freshports_GetNextValue($Sequence_User_ID, $db);
			if (IsSet($UserID)) {			
				$sql = "insert into users (id, name, password, cookie, email, " . 
						"watch_notice_id, emailsitenotices_yn, type, ip_address, number_of_days) values (";
				$sql .= "$UserID, '$UserLogin', '$Password1', '$Cookie', '$email', " .
						"'1', 'N', 'U', '" . $_SERVER["REMOTE_ADDR"] . "', " .
						"$numberofdays)";

				$errors .= "<BR>sql=" . $sql;

				$result = pg_exec($db, $sql);
				if ($result) {
					$UserCreated = 1;

					# if the mail out fails, we aren't handling it properly here.
					# we will.  eventually.
					#
					freshports_UserSendToken($UserID, $db);
				} else {
					$errors .= "OUCH! I couldn't add you to the database\n";
					$OK = 0;
				}
			} else {
				$errors .= "OUCH! I couldn't assign you a new UserID\n";
				$OK = 0;
			}

	    } else {
			$errors .= 'That User ID is already in use.  Please select a different  User ID.<BR>';
    	}
	}

	if ($UserCreated) {
		header("Location: welcome.php?origin=" . $origin);  /* Redirect browser to PHP web site */
		exit;  /* Make sure that code below does not get executed when we redirect. */
	}
} else {
	// not submit

	// we can't do this if we are submitting because it overwrites the incoming values
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');
}

   freshports_Start('New User',
               'freshports - new ports, applications',
               'FreeBSD, index, applications, ports');
?>

<SCRIPT TYPE="text/javascript">
<!--
function setfocus() { document.f.UserLogin.focus(); }
// -->
</SCRIPT>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD VALIGN="top" WIDTH="100%">
<script language="php">
if ($errors) {
echo '<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#AD0040" WIDTH=100%>
<TR>
<TD>
<TABLE WIDTH=100% BORDER=0 CELLPADDING=1>
<TR BGCOLOR="#AD0040"><TD><B><FONT color="#ffffff" size=+0>Access Code Failed!</FONT></B></TD>
</TR>
<TR BGCOLOR="#ffffff">
<TD>
  <TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=0>
  <TR VALIGN=top>
   <TD><IMG SRC="/images/warning.gif"></TD>
   <TD WIDTH=100%>
  <p>Some errors have occurred which must be corrected before your login can be created.</p>';

/*
  while (list($name, $value) = each($HTTP_POST_VARS)) {
    echo "$name = $value<BR>\n";
  }
*/
echo $errors;

echo '<p>If you need help, please post a message on the forum. </p>
 </TD>
 </TR>
 </TABLE>
</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
<BR>';
}

if (!$submit && !$errors) {
  // provide default values for an empy form.
  require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');
}

</script>

<TABLE CELLSPACING="0" BORDER="0" WIDTH="100%" CELLPADDING="5">
      <TR>
		<? echo freshports_PageBannerText("New User Details"); ?>
      </TR>
      <TR>
        <TD>

<P><BIG><BIG>Please observe the following points:</BIG>

<ul>
<li>
You must supply a valid email address. Instructions to enable your account 
will be emailed to you at that address.

<li>If you have a spam filter, please allow all
mail from <CODE CLASS="code">unixathome.org</CODE> and <CODE CLASS="code">freshports.org</CODE>.</BIG>

<li>Please disable any auto-responders for the above domains.  I get enough email
without being told when you'll be back from holiday or who else I can contact...

<li>Your browser must allow cookies for the login to work.

</ul>

<P>
Your cooperation with the above will make my life easier.  Thank you.

<hr>

<? require_once($_SERVER['DOCUMENT_ROOT'] . '/include/new-user.php'); ?>

	<hr>

    </TD>
  </TR>
</TABLE>
</TD>

	<?
	freshports_SideBar();
	?>

</TR>
</TABLE>

<?
freshports_ShowFooter();
?>

</BODY>
</HTML>
