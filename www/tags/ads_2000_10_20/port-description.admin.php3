<?
   # $Id: port-description.admin.php3,v 1.5 2001-10-20 21:50:40 dan Exp $
   #
   # Copyright (c) 1998-2001 DVL Software Limited

   require("./include/common.php");
   require("./include/freshports.php");
   require("./include/databaselogin.php");
   require("./include/getvalues.php");


function freshports_Change_Log_Port_Delete($Change_Log_Port_ID, $db) {
   // delete everything from change_log_details which 
   // belongs to this change_log_port_id

   $sql = "delete from change_log_details where change_log_port_id = $Change_Log_Port_ID";
   
   print "$sql\n<br>";                                  

   $result = mysql_query($sql, $db);
   if (!$result) {
      print mysql_error() . "<br>\n";
      exit;
   }

   $sql = "delete from change_log_port where id = $Change_Log_Port_ID";

   print "$sql\n<br>";                                  

   $result = mysql_query($sql, $db);
   if (!$result) {
      print mysql_error() . "<br>\n";
      exit;
   }

}

function freshports_Change_Log_Delete($Change_Log_ID, $db) {
   // find out what ports were changed by this commit
   // actually, we get a list of the FKs to be found in change_log_details.
   //
   $sql = "select id from change_log_port where change_log_id = $Change_Log_ID";
                                                      
   print "$sql\n<br>";                                 
                                                      
   $result = mysql_query($sql, $db);                  
                                                      
   if (!$result) {                                    
      print mysql_error() . "<br>\n";                 
      exit;                                           
   }                                                  
                                                      
   $NumRows = 0;                                      
   while ($myrow = mysql_fetch_array($result)) {
      $Change_Log_Port_ID[$NumRows] = $myrow["id"];
      print "Change_Log_Port_ID[$NumRows] = $Change_Log_Port_ID[$NumRows]\n<br>";
      $NumRows++;
   }

   // now delete those rows from change_log_port
   for ($i = 0; $i < $NumRows; $i++) {
      freshports_Change_Log_Port_Delete($Change_Log_Port_ID[$i], $db);
   }

   $sql = "delete from change_log where id = $Change_Log_ID";

   print "$sql\n<br>";                                  


   $result = mysql_query($sql, $db);
   if (!$result) {
      print mysql_error() . "<br>\n";
      exit;
   }

}


if ($HTTP_HOST = "admin.freshports.org") {
   $Admin = 1;
} else {
   $Admin = 0;
}

$Debug = 0;
if ($submit) {
   while (list($name, $value) = each($HTTP_POST_VARS)) {
      echo "$name = $value<br>\n";
   }
      
   if ($commits) {
     $CommitCount = count($commits);
     echo "CommitCount= $CommitCount<br>\n";
     while (list($key, $value) = each($commits)) {
        echo "element $key = '$value'<br>\n";

        freshports_Change_Log_Delete($value, $db);
        
     }
   }
}

$ShowEverything=1;

if (!$port || $port != strval(intval($port))) {
   $port = 0;                                     
} else {                                              
   $port = intval($port);                     
}

$sql = "select ports.id, ports.name as port, ports.id as ports_id, " .
       "categories.name as category, categories.id as category_id, ports.version as version, ".
       "ports.last_update_description as update_description, " .
       "ports.maintainer, ports.short_description, ports.long_description, UNIX_TIMESTAMP(ports.date_created) as date_created, ".
       "date_format(date_created, '$FormatDate $FormatTime') as date_created_formatted, ".
       "ports.package_exists, ports.extract_suffix, ports.needs_refresh, ports.homepage, " .
       "ports.depends_run, ports.depends_build, ports.categories, ports.status, " .
       "ports.broken, ports.forbidden " .
       "from ports, categories  ".
       "WHERE ports.id = $port ".
       "  and ports.primary_category_id       = categories.id ";

if ($Debug) {
   echo "Admin = $Admin\n";
   echo "\nsql = $sql\n";
}

$result = mysql_query($sql, $db);

if (!$result) {
   print mysql_error() . "<br>\n";
   exit;
}

$myrow = mysql_fetch_array($result);

$NumRows = mysql_num_rows($result);


   if ($NumRows) {
      $Title = $myrow["category"] . "/" . $myrow["port"];
   } else {
      $Title = "error - nothing found";
   }

   freshports_Start($Title,
               "freshports - new ports, applications",
               "FreeBSD, index, applications, ports");
?>

<table width="100%" border="0">
<tr>
  <td>
<p>This page contains the description of a single port.</p>

<p>I've just added <i>Also listed in</i>.  Some ports appear in more than one category.  
If there is no link to a category, that is because that category
is a virtual category, and I haven't catered for those yet. But <a href="changes.php3">I plan to</a></p>
<p>
<img src="images/new.gif"  alt="new feature" border="0" width="28" height="11" hspace="2">Click on 
<img src="images/logs.gif" alt="Files within this port affected by this commit" border="0" WIDTH="17" HEIGHT="20" hspace="2"> 
to see what files changed for this port in that commit.</p>
</td>
</tr>
<tr><td valign="top" width="100%">
<table width="100%" border="0">
<tr>
    <td colspan="3" bgcolor="#AD0040" height="29"><font color="#FFFFFF" size="+2">freshports - 
<?
   echo $Title;
?> 
 </font></td>
</tr>
<tr><td colspan="3" valign="top" width="100%">
<?

if ($NumRows) {

   $HideDescription=1;
   $ShowCategories=1;
   $ShowDepends=1;
   include("./include/port-basics.php");

   echo $HTML;

   echo "<dl><dd><pre>";
   echo $myrow["long_description"];
   echo "</pre></dd></dl>\n";

   $ColSpan = 3;

   if ($Admin) {
      $ColSpan++;
   }

   echo '<tr height="20"><td colspan="' . $ColSpan . '"></td></tr>' . "\n";

   echo '<tr><td>';

   if ($Admin) {
      echo '<form action="' . $PHP_SELF . '?port=' . $port. '" method="POST">';
   }

   echo '<table border="1" width="100%" CELLSPACING="0" CELLPADDING="5"bordercolor="#a2a2a2" bordercolordark="#a2a2a2" bordercolorlight="#a2a2a2">' . "\n";
   echo '<tr height="20"><td colspan="' . $ColSpan . '" bgcolor="#AD0040"><font color="#FFFFFF"><font size="+1">Commit History</font> (may be incomplete: see Changes link above for full details)</font></td></tr>' . "\n";
   echo "<tr><td><b>Date</b></td><td><b>Committer</b></td><td><b>Description</b></td>";

   if ($Admin) {
      echo "<td><b>Delete</b></td>";
   }

   echo "</tr>";

   $sql = "select change_log.id as change_log_id, " .
          "change_log_port.id   as change_log_port_id, " .
          "commit_date, update_description, committer " .
          "  from change_log, change_log_port " .
          " where change_log.id                     = change_log_port.change_log_id ".
          "   and change_log_port.port_id           =  $port". 
          " order by commit_date desc ";

   $result = mysql_query($sql, $db);
   $numrows = 0;
   while ($myrow = mysql_fetch_array($result)) {
      $numrow++;
      echo "<tr><td valign='top'><font size='-1'>" . $myrow["commit_date"]        . "</font></td>\n";
      echo "    <td valign='top'>" . $myrow["committer"]          . "</td>\n";
      echo '    <td valign="top"><a href="files.php3?id=' . $myrow["change_log_port_id"] .
                      '"><img src="images/logs.gif" alt="Files within this port affected by this commit" border="0" WIDTH="17" HEIGHT="20" hspace="2"></a>' . 
                       $myrow["update_description"] . "</td>\n";

      if ($Admin) {
         echo '<td align="center" valign="top"><input type="checkbox" name="commits[]" value="'. $myrow["change_log_id"] .'"</td>';
      }

      echo "</tr>\n";
   }
   echo "</table>\n";

   if ($Admin) {
      echo '<br>';
      echo '<input TYPE="submit" VALUE="delete selected commits" name="submit">';
      echo ' ';
      echo '<input TYPE="reset"  VALUE="reset form">';
      echo '</form>';
   }

   echo "</td></tr>\n";

}

?>

</table>
</td>
<td valign="top">

<? include("./include/side-bars.php") ?>

</td>
</tr>
</table>
 <? include("./include/footer.php") ?>
</body>
</html>
