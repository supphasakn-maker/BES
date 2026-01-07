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
	
	if($dbc->HasRecord("db_cities","name = '".$_REQUEST['name']."' AND id != ".$_REQUEST['txtID'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'city Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_REQUEST['name'],
			'country' => $_REQUEST['country']
		);
		
		if($dbc->Update("db_cities",$data,"id=".$_REQUEST['txtID'])){
			echo json_encode(array(
				'success'=>true
			));
			$city = $dbc->GetRecord("db_cities","*","id=".$_REQUEST['txtID']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"city-edit",$_REQUEST['txtID'],array("db_cities" => $city));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>