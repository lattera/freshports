<?php
	#
	# $Id: ports-deleted.php,v 1.1.2.28 2006-11-28 20:51:02 dan Exp $
	#
	# Copyright (c) 1998-2005 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/databaselogin.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/getvalues.php');

	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/freshports_page_list_ports.php');

	$page = new freshports_page_list_ports();

	$page->setDB($db);

	$page->setTitle('Deleted ports');
	$page->setDescription('These are the deleted ports');
	$page->setStatus('D');

	$page->setSQL('', $User->id);

	$page->display();
?>