<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<html>

<head>
<meta name="description" content="freshport">
<meta name="keywords" content="FreeBSD, topics, index">
<!--// DVL Software is a New Zealand company specializing in database applications. //-->
<title>freshports</title>
</head>

<body bgcolor="#ffffff" link="#0000cc">

<table width="100%" border="0">
<tr>
 <td colspan="2">
 <font size="+4">freshports</font>
 </td>
</tr>


<tr><td colspan="2">Welcome to the freshports.org test page. This site is not yet in production. We are still
testing. Information found here may be widely out of date and/or inaccurate.  Use at your own risk.
See also <a href="categories.php3">freshports by category</a>.
</td></tr>
<tr><td>
<table width="100%" border="0">
<tr>
    <td colspan="5" bgcolor="#AD0040" height="30"><font color="#FFFFFF" size="+1">freshports - last 20 ports</font></td>
  </tr>
<script language="php">

$DESC_URL = "ftp://ftp.freebsd.org/pub/FreeBSD/branches/-current/ports";

require( "/www/freshports.org/_private/commonlogin.php3");
require( "/www/freshports.org/_private/getvalues.php3");

$cache_file     =       "/tmp/freshports.org.cache.ports";
$LastUpdateFile =       "/www/freshports.org/work/msgs/lastupdate";

// make sure the value for $sort is valid

switch ($sort) {
/* sorting by port is disabled. Doesn't make sense to do this
   case "port":
      $sort = "port, updated desc";
      $cache_file .= ".port";
      break;
*/
//   case "updated":
//      $sort = "updated desc, port";
//      break;

   default:
      $sort ="updated desc, port";
      $cache_file .= ".updated";
}

srand((double)microtime()*1000000);
$cache_time_rnd =       300 - rand(0, 600);


if ($Debug) {
echo '<br>';
echo '$cache_file=', $cache_file, '<br>';
echo '$LastUpdateFile=', $LastUpdateFile , '<br>';
echo '!(file_exists($cache_file))=',     !(file_exists($cache_file)), '<br>';
echo '!(file_exists($LastUpdateFile))=', !(file_exists($LastUpdateFile)), "<br>";
echo 'filectime($cache_file)=',          filectime($cache_file), "<br>";
echo 'filectime($LastUpdateFile)=',      filectime($LastUpdateFile), "<br>";
echo '$cache_time_rnd=',                 $cache_time_rnd, '<br>';
echo 'filectime($cache_file) - filectime($LastUpdateFile) + $cache_time_rnd =', filectime($cache_file) - filectime($LastUpdateFile) + $cache_time_rnd, '<br>';
}

$UpdateCache = 0;
if (!file_exists($cache_file)) {
//   echo 'cache does not exist<br>';
   // cache does not exist, we create it
   $UpdateCache = 1;
} else {
//   echo 'cache exists<br>';
   if (!file_exists($LastUpdateFile)) {
      // no updates, so cache is fine.
//      echo 'but no update file<br>';
   } else {
//      echo 'cache file was ';
      // is the cache older than the db?
      if ((filectime($cache_file) + $cache_time_rnd) < filectime($LastUpdateFile)) {
//         echo 'created before the last database update<br>';
         $UpdateCache = 1;
      } else {
//         echo 'created after the last database update<br>';
      }
   }
}

//$UpdateCache = 1;

if ($UpdateCache == 1) {
//   echo 'time to update the cache';

$sql = "";
$sql = "select ports.name as port, ports.id as ports_id, ports.last_update as updated, " .
       "categories.name as category, categories.id as category_id, ports.version as version, ".
       "ports.committer, ports.last_update_description as description " .
       "from ports, categories  ".
       "WHERE ports.system = 'FreeBSD' ".
       "and ports.primary_category_id = categories.id ";

$sql .= "order by $sort limit 20";


$result = mysql_query($sql, $db);

$HTML = "</tr></td><tr>";

//$HTML .= '<tr><td>';

//$HTML .= '<table width="100%" border="0">';
//$HTML .= '<td><a href="ports.php3?sort=port">Port</a></td>';
$HTML .= '<td>Port</td>';
$HTML .= '<td>Description</td>';
$HTML .= '<td>Committer</td>';
$HTML .= '<td>Category</td>';
//$HTML .= '<td><a href="ports.php3?sort=updated">Updated</a></td>';
$HTML .= '<td>Updated</td>';
$HTML .= '</tr>';
$HTML .= "\n";
// get the list of topics, which we need to modify the order
$NumTopics=0;
while ($myrow = mysql_fetch_array($result)) {
	$HTML .= '<tr><td VALIGN="top"><A href="' . $DESC_URL . '/' . $myrow["category"] . '/' . $myrow["port"] . '/pkg/DESCR">';

//        $URL_Category = "http://www.freebsd.org/ports/" . $myrow["category"];
	$URL_Category = "category.php3?category=" . $myrow["category_id"];
        if ($myrow["version"]) {
           $HTML .= $myrow["version"];
        } else {
           $HTML .= $myrow["name"];
        }
        $HTML .= '</a></td>';

        $HTML .= '<td VALIGN="top">';
        $HTML .= $myrow["description"];  // separate lines in case description is null
        $HTML .= '</td>';

        $HTML .= '<td VALIGN="top"><font size="-1">';
        $HTML .= $myrow["committer"];  // separate lines in case committer is null
        $HTML .= '</font></td>';

        $HTML .= '<td VALIGN="top"><font size="-1"><a href="' . $URL_Category . '">' . $myrow["category"] . '</a></font></td>';
        $HTML .= '<td valign="top" width="240"><font size="-1">' . $myrow["updated"] . '</font></td>';
	$HTML .= "</tr>\n";
}

//$HTML .= '</tr>';

mysql_free_result($result);


//$HTML .= '</table>';
//$HTML .= '</td></tr>';

echo $HTML;

   $fpwrite = fopen($cache_file, 'w');
   if(!$fpwrite) {
      echo 'error on open<br>';
      echo "$errstr ($errno)<br>\n";
      exit;
   } else {
//      echo 'written<br>';
      fputs($fpwrite, $HTML);
      fclose($fpwrite);
   }
} else {
//   echo 'looks like I\'ll read from cache this time';
   if (file_exists($cache_file)) {
      include($cache_file);
   }
}

</script>
</table>
</td>
  <td valign="top" width="*">
  <table WIDTH="152" BORDER="1" CELLSPACING="0" CELLPADDING="5"
            bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">
        <tr>
         <td bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">Login</font></td>
        </tr>
        <tr>
         <td><script language="php">

  if ($UserID) {
   echo '<font SIZE="-1">Logged in as ', $UserID, "</font><br>";
   echo '<font SIZE="-1"><a href="customize.php3">Customize</a> / <a href="logout.php3">Logout</a></font><br>';
  } else {
   echo '<font SIZE="-1"><a href="login.php3">User Login</a></font><br>';
   echo '<font SIZE="-1"><a href="new-user.php3">Create account</a></font>';
  }
        </script>
   </td>
   </tr>
   </table>
<br>
 <table WIDTH="152" BORDER="1" BORDER="1" CELLSPACING="0" CELLPADDING="5"
            bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">        <tr>
         <td bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">This site</font></td>
        </tr>
        <tr>
    <td valign="top"><font SIZE="-1"><a href="about.html">What is freshports?</a></font><br>
                <font SIZE="-1"><a href="authors.html">About the Authors</a></font>
   </td>
   </tr>
   </table>

</td>
</tr>
</table>
</body>
</html>
