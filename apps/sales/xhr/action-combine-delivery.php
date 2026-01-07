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
	
	if(count($_POST['items'])<2){
		echo json_encode(array(
			'success'=>false,
			'msg' => "Please select more than 2 items"
		));
		
	}else{
		$orders = array();
		$customer_id = null;
		$same_customer = true;
		$amount = 0;
		
		foreach($_POST['items'] as $item){
			$order = $dbc->GetRecord("bs_orders","*","id=".$item);
			array_push($orders,$order);
			$amount += $order['amount'];
		}
		
		$data = array(
			"#id" => "DEFAULT",
			"#type" => 2,
			"delivery_date" => $order['delivery_date'],
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"#status" => 0,
			"#amount" => $amount,
			"#user" => $os->auth['id'],
			"comment" => ""
		);

		if($dbc->Insert("bs_deliveries",$data)){
			$delivery_id = $dbc->GetID();
			$code = "D-".sprintf("%08s", $delivery_id);
			$dbc->Update("bs_deliveries",array("code"=>$code),"id=".$delivery_id);
			
			foreach($orders as $order){
				$dbc->Update("bs_orders",array(
					"delivery_id"=>$delivery_id
				),"id=".$order['id']);
			}
			
			
			echo json_encode(array(
				'success'=>true,
				'msg'=> $delivery_id
			));
			
			$delivery = $dbc->GetRecord("bs_deliveries","*","id=".$delivery_id);
			$os->save_log(0,$os->auth['id'],"delivery-combine",$delivery_id,array("delivery" => $delivery));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}
	
	$dbc->Close();
?>
