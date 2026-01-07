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
	
	if($dbc->HasRecord("bs_banks","name = '".$_POST['name']."' AND id != ".$_POST['id'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'bank Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_POST['name'],
			'number' => $_POST['number'],
			'branch' => $_POST['branch'],
			'detail' => addslashes($_POST['detail']),
			'#updated' => "updated"
		);
		
		if($dbc->Update("bs_banks",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$bank = $dbc->GetRecord("bs_banks","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"bank-edit",$_POST['id'],array("bs_banks" => $bank));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>