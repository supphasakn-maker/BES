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
	
	$address = $dbc->GetRecord("os_address","*","id=".$_REQUEST['id']);
	$condtion = "priority=1";
	$condtion .= " AND contact ".(is_null($address['contact'])?"IS NULL":"= ".$address['contact']);
	$condtion .= " AND organization ".(is_null($address['organization'])?"IS NULL":"= ".$address['organization']);
	$primary = $dbc->GetRecord("os_address","*",$condtion);
	
	$data = array(
		'#priority' => 1,
		'#updated' => "NOW()"
	);
	$dbc->Update("os_address", $data,"id=".$address['id']);
	$data = array(
		'#priority' => 2,
		'#updated' => "NOW()"
	);
	$dbc->Update("os_address", $data,"id=".$primary['id']);
		
	echo json_encode(array("success"=>true));
	
	$dbc->Close();
?>