<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/abox.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$abox = new abox($dbc);
	
	$dbc->Update("contacts",array("#avatar" => "NULL"),'id='.$abox->auth['contact']['id']);
	unlink("../../../".$abox->auth['avatar']);
	
	echo json_encode(array(
		'success'=>true
	));

	$dbc->Close();
?>