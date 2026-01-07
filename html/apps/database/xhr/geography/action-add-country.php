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
	
	if($dbc->HasRecord("db_countries","name = '".$_POST['name']."'")){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'country Name is already exist.'
		));
	}else if($_POST['name']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please insert country name'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			'name' => $_POST['name'],
			'local_name' => $_POST['txtLocal'],
			'region' => 0,
			'iso' => $_POST['txtISO'],
			'iso3' => $_POST['txtISO3'],
			'#numcode' => "NULL",
			'phonecode' => $_POST['txtPhone']
		);
		
		if($dbc->Insert("db_countries",$data)){
			$country_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $country_id
			));
			
			$country = $dbc->GetRecord("db_countries","*","id=".$country_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"country-add",$country_id,array("db_countries" => $country));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>