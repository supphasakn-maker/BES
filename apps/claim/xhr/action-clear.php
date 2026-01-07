<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$unlink = "../../../".$_POST['path'];
	if($unlink!="")if(file_exists($unlink))unlink($unlink);
	
	
		echo json_encode(array(
			'success'=>true
		));
	
	
	$dbc->Close();
?>