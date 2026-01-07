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
	
	
	function load_item($dbc,$id){
		$import = $dbc->GetRecord("bs_imports","*","id=".$id);
		return $import;
	}
	
	if(is_array($_POST['id'])){
		$array = array();
		foreach($_POST['id'] as $id){
			array_push($array,load_item($dbc,$id));
		}
		echo json_encode($array);
	}else{
		echo json_encode(load_user($dbc,$_POST['id']));
	}

	
	$dbc->Close();
?>