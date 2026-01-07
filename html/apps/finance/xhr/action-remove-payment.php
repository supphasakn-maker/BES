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
	
	$payment_id = $_POST['id'];
	
	$dbc->Delete("bs_payments","id=".$payment_id);
	$dbc->Delete("bs_payment_items","payment_id=".$payment_id);
	$dbc->Delete("bs_payment_deposits","payment_id=".$payment_id);
	$dbc->Delete("bs_payment_orders","payment_id=".$payment_id);
	
	$sql = "SELECT * FROM bs_payment_deposit_use WHERE payment_id = ".$payment_id;
	$rst = $dbc->Query($sql);
	while($line = $dbc->fetch($rst)){
		$payment_deposit = $dbc->GetRecord("bs_payment_deposits","*","id=".$line['deposit_id']);
		if($payment_deposit['status']==0){
			$dbc->Update("bs_payment_deposits",array("#status"=>1),"id=".$line['deposit_id']);
		}
		
	}
	$dbc->Delete("bs_payment_deposit_use","payment_id=".$payment_id);
	
	echo json_encode(array(
		'success'=>true,
		
	));

	$dbc->Close();
?>
