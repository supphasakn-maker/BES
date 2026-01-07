<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../config/define.php";
	include_once "../include/db.php";
	include_once "../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	
	
	$dbc->Close();
?>