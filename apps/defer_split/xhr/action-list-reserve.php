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
	
	$data = array();
	
	$sql = "SELECT * FROM bs_reserve_silver WHERE supplier_id =".$_POST['supplier_id']." AND weight_actual IS NOT NULL AND import_id IS NULL";
	$rst = $dbc->Query($sql);
	while($reserve = $dbc->Fetch($rst)){
		array_push($data,$reserve);
	}
	
	echo json_encode($data);

	$dbc->Close();
?>