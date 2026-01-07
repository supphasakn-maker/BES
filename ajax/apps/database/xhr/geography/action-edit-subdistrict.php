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
	
	if($dbc->HasRecord("db_subdistricts","name = '".$_REQUEST['name']."' AND id != ".$_REQUEST['txtID'])){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'subdistrict Name is already exist.'
		));
	}else{
		$data = array(
			'name' => $_REQUEST['name'],
			'#district' => $_REQUEST['txtDistrict'],
			'#city' => $_REQUEST['txtCity'],
			'#country' => $_REQUEST['txtCountry']
		);
		
		if($dbc->Update("db_subdistricts",$data,"id=".$_REQUEST['txtID'])){
			echo json_encode(array(
				'success'=>true
			));
			$subdistrict = $dbc->GetRecord("db_subdistricts","*","id=".$_REQUEST['txtID']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"subdistrict-edit",$_REQUEST['txtID'],array("db_subdistricts" => $subdistrict));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	
	$dbc->Close();
?>