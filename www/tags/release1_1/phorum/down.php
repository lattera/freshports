<?PHP
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2000  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of the Phorum License Version 1.0.                    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

  $admin=1;
  require "./common.php";
  $title = $lForumDown;
  if(file_exists("$include_path/header_$ForumConfigSuffix.php")){
    include "$include_path/header_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/header.php";
  }
?>
<center>
<table width="<?PHP echo $default_table_width; ?>" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td <?PHP echo bgcolor($default_table_header_color); ?> valign="TOP" nowrap><font color="<?PHP echo $default_table_header_font_color; ?>">&nbsp;<?PHP echo $lForumDown; ?></font></td>
  </tr>
  <tr>
    <td width="100%" align="LEFT" valign="MIDDLE" <?PHP echo bgcolor($default_table_body_color_2); ?>><font color="<?PHP echo $default_table_body_font_color_1; ?>"><?PHP echo $lForumDownNotice; ?></font><br></td>
  </tr>
</table>
</center>
<?PHP

  if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
    include "$include_path/footer_$ForumConfigSuffix.php";    
  }
  else{
    include "$include_path/footer.php";
  }

?>