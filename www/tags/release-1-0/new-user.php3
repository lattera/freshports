<script language="php">

require( "/www/freshports.org/_private/commonlogin.php3");

if ($submit) {

// process form

/*
 while (list($name, $value) = each($HTTP_POST_VARS)) {
   echo "$name = $value<br>\n";
 }
*/
  $OK = 1;

  $errors = "";

  if ($UserName == '') {
    $errors .= "Please enter a user id.<BR>";
    $OK = 0;
  }

  if ($Password1 != $Password2) {
    $errors .= "The password was not confirmed.  It must be entered twice.<BR>";
    $OK = 0;
  } else {
    if ($Password1 == '') {
      $errors .= 'A password must be supplied<BR>';
    }
  }

  $UserCreated = 0;
  if ($OK) {
    $Cookie = UserToCookie($UserName);
//    echo "checking database\n";

    // test for existance of user id

    $sql = "select * from users where cookie = '$Cookie'";
    $result = mysql_query($sql, $db) or die('query failed');


    // create user id if not found
    if(!mysql_numrows($result)) {
 //   echo "confirmed: user id is new\n";

      if ($emailsitenotices_yn == "ON") {
         $emailsitenotices_yn_value = "Y";
      } else {
         $emailsitenotices_yn_value = "N";
      }

      $sql = "insert into users (username, password, cookie, firstlogin, lastlogin, email, " . 
             "watchnotifyfrequency, emailsitenotices_yn) values (";
      $sql .= "'$UserName', '$Password1', '$Cookie', Now(), Now(), '$email', " .
              "'$watchnotifyfrequency', '$emailsitenotices_yn_value')";

	$errors .= "<br>sql=" . $sql;

      $result = mysql_query($sql);
      if ($result) {
	$UserCreated = 1;
      } else {
	$errors .= 'Something went terribly wrong there.<br>';
/*
	$errors .= 'UserName	= '.$UserName	  . '<br>';
	$errors .= 'Password	= '.$Password1	  . '<br>';
	$errors .= 'DaysToShow	= '.$DaysToShow   . '<br>';
	$errors .= 'MaxArticles = '.$MaxArticles  . '<br>';
	$errors .= 'DaysNew	= '.$DaysNew	  . '<br>';
*/
      }

    } else {
      $errors .= 'That User ID is already in use.  Please select a different  User ID.<BR>';
    }
  }

  if ($UserCreated) {
//	echo "Ummm, I think I created that login.";
	SetCookie("visitor", $Cookie, time() + 60*60*24*120, '/');  // good for three months.
	header("Location: welcome.php3");  /* Redirect browser to PHP web site */
	exit;  /* Make sure that code below does not get executed when we redirect. */
 }
} else {
// not submit
$emailsitenotices_yn = "ON";
}
</script>

<html>

<head>
<title>freshports -- New User</title>
<meta name="description" content="freshports - new ports, applications">
<meta name="keywords" content="FreeBSD, index, applications, ports">  
<!--// DVL Software is a New Zealand company specializing in database applications. //-->
</head>

<body bgcolor="#ffffff" link="#0000cc">

<table width="100%">
  <tr>
    <td><script language="php">
if ($errors) {
echo '<table cellpadding=1 cellspacing=0 border=0 bgcolor="#AD0040" width=100%>
<tr>
<td>
<table width=100% border=0 cellpadding=1>
<tr bgcolor="#AD0040"><td><b><font color="#ffffff" size=+0>Access Code Failed!</font></b></td>
</tr>
<tr bgcolor="#ffffff">
<td>
  <table width=100% cellpadding=3 cellspacing=0 border=0>
  <tr valign=top>
   <td><img src="images/warning.gif"></td>
   <td width=100%>
  <p>Some errors have occurred which must be corrected before your login can be created.</p>';

/*
  while (list($name, $value) = each($HTTP_POST_VARS)) {
    echo "$name = $value<br>\n";
  }
*/
echo $errors;

echo '<p>If you need help, please post a message on the forum. </p>
 </td>
 </tr>
 </table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<br>';
}

if (!$submit && !$errors) {
  // provide default values for an empy form.
  require( "/www/freshports.org/_private/getvalues.php3");
}

</script>

<table cellpadding="1" cellspacing="0" border="1" bordercolor="#A2A2A2"
            bordercolordark="#A2A2A2" bordercolorlight="#A2A2A2" width="100%" cellpadding="5">
      <tr>
        <td bgcolor="#AD0040" width="695"><b><font color="#ffffff" size="+1">New User Details</font></b></td>
      </tr>
      <tr>
        <td>

<? include("/www/freshports.org/_private/new-user.inc"); ?>

        </td>
      </tr>
    </table>
    </td>
  </tr>
</form>
</table>
</a>
</body>
</html>
