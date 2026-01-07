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
	
	$info = json_decode($os->load_variable($_REQUEST['pk'],"json"),true);
	$info[$_REQUEST['name']] = base64_encode($_REQUEST['value']);
	$os->save_variable($_REQUEST['pk'],json_encode($info));
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>