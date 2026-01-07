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
	
	$dbc->Delete("os_permissions","gid=".$_POST['group_id']);
	if(isset($_POST['permission'])){
		foreach($_POST['permission'] as $app => $items){
			foreach($items as $action => $item){
				$dbc->Insert("os_permissions",array(
					'#id' => "DEFAULT",
					"#gid" => $_POST['group_id'],
					"name" => $app,
					"action" => $action
				));
			} 
		}
		
		$os->save_log(0,$_SESSION['auth']['user_id'],"group-edit-permission",$_POST['group_id'],array("permissions" => $_POST['permission']));
		
	}
	
	echo json_encode(array(
		'success'=>true,
		'msg'=> "Complete"
	));
	
	$dbc->Close();
?>