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

	$delivery = $dbc->GetRecord("bs_deliveries_bwd","*","id=".$_POST['delivery_id']);
	
	
	if($_POST['amount_total'] != $delivery['amount']){
		echo json_encode(array(
			'success'=>false,
			'msg'=>"จำนวนแท่งไม่ตรงกัน"
		));
	}else{
		
		$dbc->Update("bs_deliveries_bwd",array("#status"=>1),"id=".$delivery['id']);
		
		echo json_encode(array(
			'success'=>true
		));
	}

	$dbc->Close();
?>
