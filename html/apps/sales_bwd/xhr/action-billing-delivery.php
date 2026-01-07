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
	
	
	$bills = array();
	foreach($_POST['billing_id'] as $billing){
		if($billing != ""){
			array_push($bills,$billing);
		}
	}
	
	$data = array(
		'billing_id' => join(",",$bills),
		'#updated' => 'NOW()',
	);

	if($dbc->Update("bs_deliveries_bwd",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$delivery = $dbc->GetRecord("bs_deliveries_bwd","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"bwd-delivery-payment",$_POST['id'],array("delivery" => $delivery));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
