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
	
	
		$data = array(
			'type' => $_POST['type'],
			'topic' => addslashes($_POST['topic']),
			'detail' => addslashes($_POST['detail']),
			'#user' => $_POST['user'],
			'#updated' => 'NOW()'
		);
		
		if($dbc->Update("os_notifications",$data,"id=".$_REQUEST['txtID'])){
			echo json_encode(array(
				'success'=>true
			));
			$group = $dbc->GetRecord("os_notifications","*","id=".$_REQUEST['txtID']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"notification-edit",$_REQUEST['txtID'],array("os_notifications" => $group));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	
	
	$dbc->Close();
?>