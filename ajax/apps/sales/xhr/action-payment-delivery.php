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
	
	$json = array(
		"bank" =>$_POST['bank'],
		"payment" =>$_POST['payment'],
		"remark" => $_POST['remark']
	);
	
	
	$data = array(
		'payment_note' => json_encode($json,JSON_UNESCAPED_UNICODE),
		'#updated' => 'NOW()',
	);

	if($dbc->Update("bs_deliveries",$data,"id=".$_POST['id'])){
		echo json_encode(array(
			'success'=>true
		));
		$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$_POST['id']);
		$os->save_log(0,$_SESSION['auth']['user_id'],"delivery-payment",$_POST['id'],array("delivery" => $delivery));
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "No Change"
		));
	}
	

	$dbc->Close();
?>
