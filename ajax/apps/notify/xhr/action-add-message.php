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
	
	
	if($_POST['message']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input topic'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'#source' => $_POST['source'],
			'#destination' => $_POST['destination'],
			'#type' => 1,
			'msg' => addslashes($_POST['message']),
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'#opened' => 'NULL',
			'#acknowledge' => 'NULL'
		);
		
		if($dbc->Insert("os_messages",$data)){
			$id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $id
			));
			
			$message = $dbc->GetRecord("os_messages","*","id=".$id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"message-add",$id,array("os_messages" => $message));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>