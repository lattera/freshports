  <table WIDTH="152" BORDER="1" CELLSPACING="0" CELLPADDING="5"
            bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">
        <tr>
         <td bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">Login</font></td>
        </tr>
        <tr>

         <td><script language="php">
   switch (basename($PHP_SELF)) {
//      case "watch.php3":
//      case "watch-categories.php3":
//      case "customize.php3":
//      case "port-watch.php3":
//         $OriginLocal = '/';
//         break;

      default:
         $OriginLocal = rawurlencode($HTTP_SERVER_VARS["REQUEST_URI"]);
         break;
   }

//echo "OriginLocal = '$OriginLocal'<br>\n";
if ($UserID) {
   echo '<font SIZE="-1">Logged in as ', $UserName, "</font><br>";

   if ($EmailBounceCount > 0) {
      echo '<img src="/images/warning.gif"><img src="/images/warning.gif"><img src="/images/warning.gif"><br>';
      echo '<font SIZE="-1">your email is <a href="bouncing.php3?origin=' . $OriginLocal. '">bouncing</a></font><br>';
      echo '<img src="/images/warning.gif"><img src="/images/warning.gif"><img src="/images/warning.gif"><br>';
   }
   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/customize.php3',        "?origin=$OriginLocal", "Customize"              ) . '</font><br>';


   # for a logout, where we go depends on where we are now
   #
   switch ($PHP_SELF) {
      case "customize.php3":
      case "watch-categories.php3":
      case "watch.php3":
         $args = "?origin=$OriginLocal";
         break;

      default:
         $args = '';
   }
   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/logout.php3',           $args,                  "Logout"                 ) . '</font><br>';


   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/watch-categories.php3', '',                     "watch list - Categories") . '</font><br>';
   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/watch.php3',            '',                     "your watched ports"     ) . '</font><br>';
  } else {
   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/login.php3',            "?origin=$OriginLocal", "User Login"             ) . '</font><br>';
   echo '<font SIZE="-1">' . freshports_SideBarHTMLParm($PHP_SELF, '/new-user.php3',         "?origin=$OriginLocal", "Create account"         ) . '</font><br>';
  }
?>
   </td>
   </tr>
   </table>
<br>

<table WIDTH="152" BORDER="1" CELLSPACING="0" CELLPADDING="5"
            bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">        <tr>
         <td bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">Ports</font></td>
        </tr>
        <tr>
    <td valign="top">
<?
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/",                   "Home")            . '</font><br>';
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/ports-new.php3",     "Brand new ports") . '</font><br>';
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/ports-deleted.php3", "Deleted ports")   . '</font><br>';
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/categories.php3",    "Categories")      . '</font><br>';
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/search.php3",        "Search")          . '</font><br>';
       echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/stats.php3",         "Port statistics") . '</font><br>';
?>
   </td>
   </tr>
   </table>
<br>
 <table WIDTH="152" BORDER="1" CELLSPACING="0" CELLPADDING="5"
            bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">        <tr>
         <td bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">This site</font></td>
        </tr>
        <tr>
    <td valign="top">
<?
        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/about.php3",          "What is freshports?") . '</font><br>';
        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/authors.php3",        "About the Authors")   . '</font><br>';
#        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/phorum/list.php?f=3", "Feedback Phorum")     . '</font><br>';
        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/inthenews.php3",      "In the news")         . '</font><br>';
        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/changes.php3",        "Changes")             . '</font><br>';
        echo '<font SIZE="-1">' . freshports_SideBarHTML($PHP_SELF, "/privacy.php3",        "Privacy")             . '</font><br>';
?>
    </td>
   </tr>
   </table>
