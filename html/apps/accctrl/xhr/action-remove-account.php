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
	$aResult = array();
	
	foreach($_POST['items'] as $item){
		$account = $dbc->GetRecord("os_accounts","*","id=".$item);
		if($dbc->HasRecord("os_groups","account=".$item)){
			array_push($aResult,array(
				"id" => $account['id'],
				"name" => $account['name'],
				"deleted" => false 
			));
		}else{
			array_push($aResult,array(
				"id" => $account['id'],
				"name" => $account['name'],
				"deleted" => true 
			));
			
			$account = $dbc->GetRecord("os_accounts","*","id=".$item);
			$organization = $dbc->GetRecord("os_organizations","*","id=".$account['org_id']);
			
			$dbc->Delete("os_accounts","id=".$item);
			$dbc->Delete("os_organizations","id=".$account['org_id']);
			$dbc->Delete("os_address","organization=".$organization['id']);
			
			$os->save_log(0,$_SESSION['auth']['user_id'],"account-delete",$id,array("accounts" => $account));
		}
	}
	
	echo json_encode($aResult);
	$dbc->Close();
?>