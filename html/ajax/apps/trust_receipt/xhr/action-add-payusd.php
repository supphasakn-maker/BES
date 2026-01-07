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
	
	
	if($_POST['date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please Delivery Date'
		));
	}else if($_POST['paid_thb']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input paid'
		));
	}else{
		
		
		$data = array(
			"#id" => "DEFAULT",
			"date" => $_POST['date'],
			"#transfer_id" => $_POST['transfer_id'],
			"#purchase_id" => $_POST['purchase_id'],
			"interest_start" => $_POST['interest_start'],
			"interest_end" => $_POST['interest_end'],
			"#rate_interest" => $_POST['rate_interest'],
			"#interest" => $_POST['interest'],
			"#interest_day" => $_POST['interest_day'],
			"#piad_usd" => 0,
			"#paid_thb" => $_POST['paid_thb']
		);
		
		
		if($dbc->Insert("bs_usd_payment",$data)){
			$payment_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $payment_id
			));
			
			$dbc->Update("bs_purchase_usd",array(
				"#unpaid" => $_POST['remain']
			),"id = ".$_POST['purchase_id']);
			
	
			
			$payment = $dbc->GetRecord("bs_usd_payment","*","id=".$payment_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"payment-add",$payment_id,array("payment" => $payment));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>