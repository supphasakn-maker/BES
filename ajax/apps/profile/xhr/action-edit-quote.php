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
	
	$user = $dbc->GetRecord("users","*","id=".$_REQUEST['txtID']);
	$setting = json_decode($user['setting'],true);
	
	$setting['quote'] = array(
		"title" => base64_encode($_REQUEST['txtTitle']),
		"detail" => base64_encode($_REQUEST['txtDetail'])
	);
	
	$data = array(
		'#updated' => "NOW()",
		'setting' => json_encode($setting)
	);
	
	$dbc->Update("users", $data,"id=".$_REQUEST['txtID']);
		
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Passed"
	));
	
	$dbc->Close();
?>