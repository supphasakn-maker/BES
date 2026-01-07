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
	}else if($_POST['paid']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input paid'
		));
	}else{
		$data = array(
			"#id" => "DEFAULT",
			"date" => $_POST['date'],
			"#transfer_id" => $_POST['transfer_id'],
			"currency" => 'THB',
			"#principle" => $_POST['principle'],
			"#interest" => $_POST['interest'],
			"#paid" => $_POST['paid'],
			"date_from" => $_POST['interest_start'],
			"date_to" => $_POST['interest_end'],
			"#rate_interest" => $_POST['rate_interest'],
			"#rate_counter" => "NULL"
		);
		
		
		if($dbc->Insert("bs_transfer_payments",$data)){
			$payment_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $payment_id
			));
			
			$dbc->Update("bs_transfers",array("#paid_thb" => "paid_thb+".$_POST['paid']),"id = ".$_POST['transfer_id']);
			
			$payment = $dbc->GetRecord("bs_usd_payment","*","id=".$payment_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"tr-payment-add",$payment_id,array("payment" => $payment));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>