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
	
	$user = $dbc->GetRecord("users","*","id=".$_SESSION['auth']['user_id']);
	$setting = json_decode($user['setting'],true);
	$setting['lang'] = $_REQUEST['cbbLang'];
	
	$data = array(
		"setting" => json_encode($setting),
		"#updated" => "NOW()"
	);
	
	
	if($dbc->Update("users", $data,"id=".$_SESSION['auth']['user_id'])){
		echo json_encode(array(
			'success'=>true
		));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Update Error"
		));
	}
	
	$dbc->Close();
?>