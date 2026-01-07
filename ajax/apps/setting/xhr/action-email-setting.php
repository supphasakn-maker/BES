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
	$setting['email'] = array(
		"email" => $_REQUEST['txtEmail'],
		"in" => array(
			"type" => $_REQUEST['cbbType'],
			"server" => $_REQUEST['txtIncoming'],
			"username" => $_REQUEST['txtUsername'],
			"password" => $_REQUEST['txtPassword'],
			"security" => $_REQUEST['cbbSecurity'],
			"port" => $_REQUEST['txtIncomingPort']
		),
		"samesetting" => isset($_REQUEST['chkAuth'])?true:false,
		"out" => array(
			"type" => 'smtp',
			"server" => $_REQUEST['txtOutgoing'],
			"username" => isset($_REQUEST['chkAuth'])?$_REQUEST['txtUsername']:$_REQUEST['txtOutUsername'],
			"password" => isset($_REQUEST['chkAuth'])?$_REQUEST['txtPassword']:$_REQUEST['txtOutPassword'],
			"security" => $_REQUEST['cbbSMTPSecurity'],
			"port" => $_REQUEST['txtOutgoingPort']
		)
	);
	
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