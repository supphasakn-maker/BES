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
	
	$mapping_item = $dbc->GetRecord("bs_mapping_silver_orders","*","id=".$_POST['id']);
	if(!$dbc->HasRecord("bs_mapping_silver_orders","order_id = ".$mapping_item['order_id']." AND mapping_id IS NOT NULL")){
		echo json_encode(array(
			'success'=>false
		));
	}else{
		$dbc->Delete("bs_mapping_silver_orders","id=".$_POST['id']);
		echo json_encode(array(
			'success'=>true
		));
		
	}
	$dbc->Close();
?>
