<?
require( "./_private/commonlogin.php3");
require( "./_private/getvalues.php3");
require( "./_private/freshports.php3");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<html>

<head>
<meta name="description" content="freshports - new ports, applications">
<meta name="keywords" content="FreeBSD, index, applications, ports">  
<!--// DVL Software is a New Zealand company specializing in database applications. //-->
<title>freshports</title>
</head>

 <? include("./_private/header.inc") ?>
<table width="100%" border="0">
<tr><td colspan="2">Welcome to the freshports.org where you can find the latest information on your favourite
ports.
</td></tr>
  <tr>
    <td colspan="2">Note: <font size="-1">[refresh]</font> indicates a port for which the Makefile, 
                  pkg/DESC, or pkg/COMMENT has changed and has not yet been updated within FreshPorts.
    </td>
  </tr>
<tr><td valign="top" width="100%">
<table width="100%" border="1">
<tr>
    <td colspan="5" bgcolor="#AD0040" height="30">
        <font color="#FFFFFF" size="+1">freshports - change log
        <? echo ($StartAt + 1) . " - " . ($StartAt + 20) ?></font></td>
  </tr>
<script language="php">

$sql = "select id, date_format(date_added, '%T') as date_added, commit_date, committer, update_description from change_log order by id desc limit 60";

echo $sql;

$result = mysql_query($sql, $db);

if (!$result) {
   echo mysql_errno().": ".mysql_error()."<BR>";
} else {

   $i = 0;
   while ($myrow = mysql_fetch_array($result)) {
//      echo "<tr><td>" . $myrow["change_log_id"] . "</td><td>" . $myrow["port_id"] . "</td></tr>";
      $rows[$i] = $myrow;
      $i++;
   }
   echo "<tr><td colspan='6'>$i records found</td><td></td></tr>";

   $NumRows = $i;
   for ($i = 0; $i < $NumRows; $i++) {
      $myrow = $rows[$i];
      echo "<tr>\n";
      echo "  <td>" . $myrow["id"]			. "</td>";
      echo "  <td>" . $myrow["date_added"]		. "</td>";
      echo "  <td>" . $myrow["commit_date"]		. "</td>";
      echo '  <td><a href="change_log_port.php3?ChangeLogID=' . $myrow["id"] . '">More</a></td>';
      echo "  <td>" . $myrow["committer"]		. "</td>";
      echo "  <td>" . $myrow["update_description"]	. "</td>";
      echo "</tr>\n";
   }
}

</script>
</table>
</td>
  <td valign="top" width="*">
   <? include("./_private/side-bars.php3") ?>
 </td>
</tr>
</table>
</tr>
</table>
<? include("./_private/footer.inc") ?>
</body>
</html>
