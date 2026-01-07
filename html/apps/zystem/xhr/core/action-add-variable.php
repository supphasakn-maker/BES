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
	
	
	if($dbc->HasRecord("os_variable","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Variable Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'value' => addslashes($_POST['value']),
			'#updated' => 'NOW()'
		);
		
		if($dbc->Insert("os_variable",$data)){
			$variable_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $variable_id
			));
			
			$variable = $dbc->GetRecord("os_variable","*","id=".$variable_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"variable-add",$variable_id,array("variables" => $variable));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>