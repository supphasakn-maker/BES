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
	
	if($dbc->HasRecord("db_districts","name = '".$_REQUEST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'district Name is already exist.'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_REQUEST['name'],
			'#city' => $_REQUEST['txtCity'],
			'#country' => $_REQUEST['txtCountry']
		);
		
		if($dbc->Insert("db_districts",$data)){
			$district_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $district_id
			));
			
			$district = $dbc->GetRecord("db_districts","*","id=".$district_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"district-add",$district_id,array("db_districts" => $district));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>