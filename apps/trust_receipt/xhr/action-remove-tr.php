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
	
	$tr_us = 

	$usd = $dbc->GetRecord("bs_transfer_usd","*","purchase_id=".$_POST['id']);
	$transfer = $dbc->GetRecord("bs_transfers","*","id=".$usd['transfer_id']);
	$purchase = $dbc->GetRecord("bs_purchase_usd","*","id=".$usd['purchase_id']);

	$value_usd_fixed = $transfer['value_usd_fixed'] - $purchase['amount'];
	$value_usd_nonfixed = $transfer['value_usd_nonfixed'] + $purchase['amount'];
	$value_thb_fixed = $transfer['value_thb_fixed'] - ($purchase['amount']*$purchase['rate_exchange']);
	$value_thb_premium = $transfer['value_thb_premium'] - $usd['premium'];
	$value_thb_net = $transfer['value_thb_net'] - $usd['premium'] -($purchase['amount']*$purchase['rate_exchange']);
	
	
	$data = array(
		"#value_usd_fixed" => $value_usd_fixed ,
		"#value_usd_nonfixed" => $value_usd_nonfixed,
		"#value_thb_fixed" => $value_thb_fixed,
		"#value_thb_premium" => $value_thb_premium,
		"#value_thb_net" => $value_thb_net,
		"#updated" => 'NOW()'
	);
	
	$dbc->Update("bs_transfers",$data,"id = ".$transfer['id']);
	$dbc->Delete("bs_transfer_usd","purchase_id=".$_POST['id']);
	
	$os->save_log(0,$_SESSION['auth']['user_id'],"tr-usd-remove",$purchase['id'],array("usd" => $usd,"transfer" => $transfer));		

	echo json_encode(array(
		'success'=>true,
		'data'=>$data
	));

	$dbc->Close();
?>
