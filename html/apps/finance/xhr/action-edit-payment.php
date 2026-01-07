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
	}else if($_POST['date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุเวลา'
		));
	}else{
		$data = array(
			"datetime" => $_POST['date']." ".$_POST['time'],
			"payment" => $_POST['payment'],
			"#customer_id" => $_POST['customer_id'],
			"customer_bank" => $_POST['customer_bank'],
			"#bank_id" => $_POST['bank_id'],
			"ref" => $_POST['ref'],
			"#amount" => $_POST['amount'],
			'#updated' => 'NOW()',
		);
		
		if($_POST['date_active']!=""){
			$data['date_active'] = $_POST['date_active'];
		}else{
			$data['#date_active'] = "NULL";
		}

		if($dbc->Update("bs_payments",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$payment = $dbc->GetRecord("bs_payments","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"payment-edit",$_POST['id'],array("payments" => $payment));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}

	$dbc->Close();
?>
