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
	
	
	$dbc->update("bs_customers",array("imgs"=>json_encode($_POST['files'])),"id=".$_POST['id']);
	
	echo json_encode(array(
		'success'=>true,
		'msg' => "Remove Complete"
	));

	$dbc->Close();
?>