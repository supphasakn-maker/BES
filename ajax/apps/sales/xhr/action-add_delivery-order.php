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

	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);
	$customer = $dbc->GetRecord("bs_customers","*","id=".$order['customer_id']);
	if($_POST['delivery_date'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please input delivery date"
		));
	}else{

		$data = array(
			"#id" => "DEFAULT",
			"#type" => 1,
			"delivery_date" => $_POST['delivery_date'],
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"#status" => 0,
			"#amount" => $order['amount'],
			"#user" => $os->auth['id'],
			"comment" => ""
		);
		
		if($customer['default_bank']!=""){
			$json = array(
				"bank" =>$customer['default_bank'],
				"payment" =>$customer['default_payment'],
				"remark" => ''
			);
			$data['payment_note'] =json_encode($json,JSON_UNESCAPED_UNICODE);
		}
		
		

		if($dbc->Insert("bs_deliveries",$data)){
			$delivery_id = $dbc->GetID();
			$code = "D-".sprintf("%07s", $delivery_id);
			$dbc->Update("bs_deliveries",array("code"=>$code),"id=".$delivery_id);
			echo json_encode(array(
				'success'=>true,
				'code' => $code
			));
			$dbc->Update("bs_orders",array("delivery_id"=>$delivery_id,"delivery_date" => $_POST['delivery_date']),"id=".$order['id']);
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	

	$dbc->Close();
?>
