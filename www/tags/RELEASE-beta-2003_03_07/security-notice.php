<?
	# $Id: security-notice.php,v 1.1.2.3 2003-03-06 22:04:48 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/databaselogin.php');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/getvalues.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/watch-lists.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/commit.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/security_notice.php');

	$PageTitle = 'Security Notice';

	freshports_Start($PageTitle,
					$FreshPortsName . ' - new ports, applications',
					'FreeBSD, index, applications, ports');
	$Debug = 0;
#	phpinfo();

	$message_id = AddSlashes($_REQUEST[message_id]);

	$SecurityNotice = new SecurityNotice($db);


	if (IsSet($_REQUEST[submit])) {
		$result = "ROLLBACK";
		pg_exec($db, "BEGIN");
		$description = AddSlashes($_REQUEST[description]);
		if ($SecurityNotice->Create($User->id, $message_id, $description, $_SERVER[REMOTE_ADDR])) {
			if ($SecurityNotice->FetchByMessageID($message_id)) {
				if (pg_exec($db, 'UPDATE housekeeping SET refresh_now = 2')) {
					$result = "COMMIT";
				}
			}
		}

		pg_exec($db, $result);
		if ($result == "ROLLBACK") {
			unset($SecurityNotice->id);
		}
	} else {
		$SecurityNotice->FetchByMessageID($message_id);
	}
?>

<TABLE width="<? echo $TableWidth ?>" border="0" ALIGN="center">
<TR><TD VALIGN=TOP WIDTH="100%">
<TABLE WIDTH="100%" border="0">
<TR>
	<? echo freshports_PageBannerText($PageTitle); ?>
<TR><TD>
<?php
	if (IsSet($SecurityNotice->id)) {
		echo '<p>' . freshports_Security_Icon() . ' This commit has been marked as security related</p>';
	} else {
		if ($User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) {
			echo 'This page allows you to mark a commit as being security related.  Such commits will be included on the Security Notification report' . "\n";
			echo 'mailed out to users and marked with a <a href="/faq.php">security lock</a> whereever that commit appears.';
		} else {
			echo 'This commit is not security related.';
		}
	}
?>
</TD></TR>
	<?
	$Debug = 0;

	$Commit = new Commit($db);
	if ($Commit->FetchByMessageId($message_id) == $message_id) {

		$HTML .= '<TR><TD COLSPAN="3" BGCOLOR="#AD0040" HEIGHT="0">' . "\n";
		$HTML .= '   <FONT COLOR="#FFFFFF"><BIG>' . FormatTime($Commit->commit_date, 0, "D, j M Y") . '</BIG></FONT>' . "\n";
		$HTML .= '</TD></TR>' . "\n\n";

		$HTML .= "<TR><TD>\n";

		$HTML .= '<SMALL>';
		$HTML .= '[ ' . $Commit->commit_time . ' ' . $Commit->committer . ' ]';
		$HTML .= '</SMALL>';
		$HTML .= '&nbsp;';
		$HTML .= freshports_Email_Link($Commit->message_id);

		$HTML .= "\n<BLOCKQUOTE>\n";
		$HTML .= freshports_PortDescriptionPrint($Commit->commit_description, $Commit->encoding_losses);
		$HTML .= "\n</BLOCKQUOTE>\n</TD></TR>\n\n\n";

		$HTML .= '<tr><td height=20><hr width="97%" align="center"></tr></td>';
	} else {
		$HTML = '<TR><TD>I did not find that commit</TD></TR>';
	}

	echo $HTML;
?>

<tr><td>
<?php
	GLOBAL $freshports_Tasks_SecuritydNoticeAdd;

	if (IsSet($SecurityNotice->id)) {
		echo '<h2>Notification reason</h2>';
	} else {
		if ($User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) {
?>
<p>
Please enter your reasoning for marking the above commit as a security issue.
</p>
<?php
		}
	}

	if (IsSet($SecurityNotice->id) || $User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) {
?>

<FORM ACTION="<? echo $_SERVER["PHP_SELF"]; ?>" method="POST">
	<TEXTAREA NAME="description" ROWS="10" COLS=60"<?php
	if (IsSet($SecurityNotice->description) || !$User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) echo ' readonly'; ?>><?php
	if (IsSet($SecurityNotice->description)) {
		echo $SecurityNotice->description;
	} else {
		echo 'enter security issue description here';
	}
?></TEXTAREA>
	<BR>
<?php
	if (!IsSet($SecurityNotice->id) && $User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) {
?>
	<INPUT TYPE="submit" VALUE="Save Security Info" NAME="submit">
<?php
	}
?>
	<INPUT TYPE="hidden" NAME="message_id" VALUE="<? echo $message_id; ?>">
</FORM>
<?php
	}

	if (IsSet($SecurityNotice->id)) {
		if ($User->IsTaskAllowed(FRESHPORTS_TASKS_SECURITY_NOTICE_ADD)) {
			$UserAlt = new User($db);
			$UserAlt->Fetch($SecurityNotice->user_id);
?>
<h2>Audit trail</h2>
<table border=1 CELLSPACING="0" CELLPADDING="5">
<tr><td><b>Date marked</b></td><td><b>User Name</b></td><td><b>IP Address</b></td><td><b>e-mail</b></td><td><b>status</b></td></tr>
<tr>
<td><?php echo $SecurityNotice->date_added; ?></td>
<td><?php echo $UserAlt->name; ?></td>
<td><?php echo $SecurityNotice->ip_address; ?></td>
<td><?php echo $UserAlt->email; ?></td>
<td><?php echo $SecurityNotice->status; ?></td>
</tr>
</td>
</table>

<?php
		}
		echo freshports_Security_Icon() . 'This commit is set as security related';
	}
?>
</td></tr>
<?php
	?>
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
