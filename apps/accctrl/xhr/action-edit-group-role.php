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
	
	/*
	$dbc->Delete("permissions","gid=".$_REQUEST['txtID']);
	if(isset($_REQUEST['permission'])){
		foreach($_REQUEST['permission'] as $app => $items){
			foreach($items as $action => $item){
				$dbc->Insert("permissions",array(
					'#id' => "DEFAULT",
					"#gid" => $_REQUEST['txtID'],
					"name" => $app,
					"action" => $action
				));
			} 
		}
		
		$os->save_log(0,$_SESSION['auth']['user_id'],"group-edit-permission",$_REQUEST['txtID'],array("permissions" => $_REQUEST['permission']));
		
	}
	*/
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Complete"
	));
	
	$dbc->Close();
?>