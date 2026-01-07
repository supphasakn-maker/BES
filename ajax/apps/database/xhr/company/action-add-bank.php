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
	
	if($dbc->HasRecord("bs_banks","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'bank Name is already exist.'
		));
	}else if($_POST['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert bank name'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'number' => $_POST['number'],
			'branch' => $_POST['branch'],
			'detail' => addslashes($_POST['detail']),
			'#icon' => "NULL",
			'#created' => "created",
			'#updated' => "updated"
		);
		
		if($dbc->Insert("bs_banks",$data)){
			$bank_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $bank_id
			));
			
			$bank = $dbc->GetRecord("bs_banks","*","id=".$bank_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"bank-add",$bank_id,array("bs_banks" => $bank));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>