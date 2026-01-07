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

	$order = $dbc->GetRecord("bs_quick_orders","*","id=".$_POST['id']);
	$customer = $dbc->GetRecord("bs_customers","*","id=".$order['customer_id']);
	$total = $order['amount']*$order['price'];
	if($order['vat_type']=="2"){
		$vat = $total*0.07;
	}else{
		$vat = 0;
	}
	$net = $total+$vat;
	$order_date = strtotime($order['created']);
	
	$data = array(
		'#id' => "DEFAULT",
		"#customer_id" => $customer['id'],
		"customer_name" => $customer['name'],
		"date" => $order['created'],
		"#sales" => is_null($customer['default_sales'])?"NULL":$customer['default_sales'],
		"#user" => $order['sales'],
		'#type' => 1,
		"#parent" => 'NULL',
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		'#amount' => $order['amount'],
		'#price' => $order['price'],
		'#vat_type' => $order['vat_type'],
		'#vat' => $vat,
		'#total' => $total,
		'#net' => $net,
		"#status" => 1,
		"comment" => $order['remark'],
		"shipping_address" => $customer['shipping_address'],
		"billing_address" => $customer['billing_address'],
		"#rate_spot" => $order['rate_spot'],
		"#rate_exchange" => $order['rate_exchange'],
		"currency" => "THB",
		'delivery_time' => $_POST['delivery_time'],
		"info_payment" => $customer['default_payment'],
		"info_contact" => $customer['contact'],
		"#product_id" => $order['product_id']
	);
	
	if(isset($_POST['delivery_lock']) || $_POST['delivery_date']==""){
		$data['#delivery_date'] = "NULL";
	}else{
		$data['delivery_date'] = $_POST['delivery_date'];
	}
	
	
	
			
	if($dbc->Insert("bs_orders",$data)){
		$order_id = $dbc->GetID();
		
		$code = "O-".sprintf("%07s", $order_id);
		$dbc->Update("bs_orders",array("code"=>$code),"id=".$order_id);
		$dbc->Update("bs_quick_orders",array(
			"#status"=>2,
			"#order_id"=>$order_id
		),"id=".$_POST['id']);
		
		
		
		echo json_encode(array(
			'success'=>true,
			'msg'=> "คำสั่งซื้อ " . $code,
			'order_id'=>$order_id
		));
		
		$order = $dbc->GetRecord("bs_orders","*","id=".$order_id);
		$os->save_log(0,$os->auth['id'],"order-add-byquickorder",$order_id,array("order" => $order));
		
		if(!is_null($order['delivery_date'])){

			$data = array(
				"#id" => "DEFAULT",
				"#type" => 1,
				"delivery_date" => $order['delivery_date'],
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

			
			$dbc->Insert("bs_deliveries",$data);
			$delivery_id = $dbc->GetID();
			
			$code = "D-".sprintf("%07s", $delivery_id);
			$dbc->Update("bs_deliveries",array("code"=>$code),"id=".$delivery_id);
			$dbc->Update("bs_orders",array("delivery_id"=>$delivery_id),"id=".$order['id']);
		}
		
	}else{
		echo json_encode(array(
			'success'=>false,
			'msg' => "Insert Error"
		));
	}
	
	
	
	

	$dbc->Close();
?>
