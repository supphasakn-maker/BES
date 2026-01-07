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
	
	//$user_id = $_SESSION['auth']['user_id'];
	$google_id = $_POST['google_id'];
	

	$dbc->Update("contacts",array("google"=>$_POST['google_id']),"id=".$os->auth['contact']['id']);
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	$dbc->Close();
?>