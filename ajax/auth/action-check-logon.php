<?php
	session_start();
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	if(isset($_SESSION['auth'])){
		echo json_encode(true);
	}else{
		echo json_encode(false);
	}
	
	$dbc->Close();
?>