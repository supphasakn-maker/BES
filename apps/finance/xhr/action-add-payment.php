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
			'msg'=>'โปรดระบุวันที่'
		));
	}else if($_POST['time']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุเวลา'
		));
	}else if($_POST['amount']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุจำนวน'
		));
	}else{
		$data = array(
			'#id' => "DEFAULT",
			"datetime" => $_POST['date']." ".$_POST['time'],
			"payment" => $_POST['payment'],
			"#customer_id" => $_POST['customer_id'],
			"customer_bank" => $_POST['customer_bank'],
			"#bank_id" => $_POST['bank_id'],
			"ref" => $_POST['ref'],
			"#amount" => $_POST['amount'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#approved" => 'NULL',
			"#status" => 0,
			"#approver" => 'NULL'
			
		);
		
		if($_POST['date_active']!=""){
			$data['date_active'] = $_POST['date_active'];
		}else{
			$data['#date_active'] = "NULL";
		}

		if($dbc->Insert("bs_payments",$data)){
			$payment_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $payment_id
			));

			$payment = $dbc->GetRecord("bs_payments","*","id=".$payment_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"payment-add",$payment_id,array("payments" => $payment));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
