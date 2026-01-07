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
	$sql = "SELECT DISTICT FROM bs_bank_statement WHERE bank_id = ".$_POST['bank_id']."";
	$rst = $dbc->Query($sql);
	while($statement = $dbc->Fetch($rst)){
		array_push($data,$statement);
	}
	
	echo json_encode($data);
	
?>