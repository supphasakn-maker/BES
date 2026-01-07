<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/concurrent.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$concurrent = new concurrent($dbc);
	$google_id = $_POST['google_id'];
	
	if($dbc->HasRecord("os_contacts","google LIKE '".$google_id."'")){
		$contact = $dbc->GetRecord("os_contacts","id","google LIKE '".$google_id."'");
		$user = $dbc->GetRecord("os_users","*","contact = ".$contact['id']);
		echo $concurrent->login($user['id']);
	}else{
		echo json_encode(array(
			"success" => false,
			"msg" => "Sorry users not found!"
		));
		
	}
	$dbc->Close();
?>