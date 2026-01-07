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
	
	foreach($_POST['items'] as $item){
		$organization = $dbc->GetRecord("os_organizations","*","id=".$item);
		$address = $dbc->GetRecord("os_address","*","organization=".$organization['id']);
	
		$dbc->Delete("os_organizations","id=".$item);
		$dbc->Delete("os_address","organization=".$item);
		$os->save_log(0,$_SESSION['auth']['user_id'],"organization-delete",$organization['id'],array("organizations" => $organization,"address" => $address));
	}
	
	$dbc->Close();
?>