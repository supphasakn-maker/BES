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
			'#source' => $_POST['source'],
			'#destination' => $_POST['destination'],
			'msg' => addslashes($_POST['message']),
			'#updated' => 'NOW()'
		);
		
		if($dbc->Update("os_messages",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$group = $dbc->GetRecord("os_messages","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"message-edit",$_POST['id'],array("os_messages" => $group));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	
	
	$dbc->Close();
?>