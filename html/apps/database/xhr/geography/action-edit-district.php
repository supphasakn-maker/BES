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
	
	if($dbc->HasRecord("db_districts","name = '".$_REQUEST['name']."' AND id != ".$_REQUEST['txtID'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'district Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_REQUEST['name'],
			'#city' => $_REQUEST['txtCity'],
			'#country' => $_REQUEST['txtCountry']
		);
		
		if($dbc->Update("db_districts",$data,"id=".$_REQUEST['txtID'])){
			echo json_encode(array(
				'success'=>true
			));
			$district = $dbc->GetRecord("db_districts","*","id=".$_REQUEST['txtID']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"district-edit",$_REQUEST['txtID'],array("db_districts" => $district));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>