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
	
	$payment = $dbc->GetRecord("bs_transfer_payments","*","id=".$_POST['id']);
	$dbc->Delete("bs_transfer_payments","id=".$_POST['id']);
	
	$field = $payment['currency']=="USD"?"paid_usd":"paid_thb";
	
	$dbc->Update("bs_transfers",array("#".$field => $field."-".$payment['paid']),"id = ".$payment['transfer_id']);
	$os->save_log(0,$_SESSION['auth']['user_id'],"tr-payment-remove",$payment['id'],array("payment" => $payment));		

	echo json_encode(array(
		'success'=>true
	));
	

	$dbc->Close();
?>
