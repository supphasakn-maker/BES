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
	
	
	if($_POST['topic']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input topic'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'type' => $_POST['type'],
			'topic' => addslashes($_POST['topic']),
			'detail' => addslashes($_POST['detail']),
			'#user' => $_POST['user'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'#acknowledge' => 'NULL'
		);
		
		if($dbc->Insert("os_notifications",$data)){
			$id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $id
			));
			
			$notification = $dbc->GetRecord("os_notifications","*","id=".$id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"notification-add",$id,array("os_notifications" => $notification));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>