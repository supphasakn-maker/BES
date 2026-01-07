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
		$group = $dbc->GetRecord("os_groups","*","id=".$item);
		if($dbc->HasRecord("os_users","gid=".$item) || $item == 1){
			array_push($aResult,array(
				"id" => $account['id'],
				"name" => $account['name'],
				"deleted" => false 
			));
		}else{
			array_push($aResult,array(
				"id" => $account['id'],
				"name" => $account['name'],
				"deleted" => false 
			));
			$dbc->Delete("os_groups","id=".$item);
			$dbc->Delete("os_permissions","gid=".$item);
			$os->save_log(0,$_SESSION['auth']['user_id'],"group-delete",$id,array("groups" => $group));
		}
	}
	echo json_encode($aResult);
	$dbc->Close();
?>