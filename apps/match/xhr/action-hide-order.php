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
	
	if($dbc->Update("bs_orders",array("#flag_hide"=>1),"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
	}else{
		echo json_encode(array(
			'success'=>false
		));
		
	}
	$dbc->Close();
?>
