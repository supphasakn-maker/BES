<?php
	session_start();
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	if($dbc->HasRecord("os_variable","name = '".$_POST['name']."' AND id != ".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Variable Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'value' => addslashes($_POST['value']),
			'#updated' => 'NOW()'
		);
		
		if($dbc->Update("os_variable",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$variable = $dbc->GetRecord("os_variable","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"variable-edit",$_POST['id'],array("variables" => $variable));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>