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


	if($_POST['customer_id'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please select customer'
		));
	}else if($_POST['amount'] == "" || $_POST['amount'] == 0){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input amount'
		));
	}else if($_POST['price'] == "" || $_POST['price'] == 0){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input price'
		));
	}else{
		$customer = $dbc->GetRecord("bs_customers","*","id=".$_POST['customer_id']);
		$total = $_POST['amount']*$_POST['price'];
		if($_POST['vat_type']=="2"){
			$vat = $total*0.07;
		}else{
			$vat = 0;
		}
		$net = $total+$vat;
		

		
		$data = array(
			'#id' => "DEFAULT",
			"#customer_id" => $_POST['customer_id'],
			"customer_name" => $customer['name'],
			"date" => $_POST['date'],
			"#sales" => isset($_POST['sales'])?$_POST['sales']:(is_null($customer['default_sales'])?"NULL":$customer['default_sales']),
			"#user" => $os->auth['id'],
			'#type' => 1,
			"#parent" => 'NULL',
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			'#amount' => $_POST['amount'],
			'#price' => $_POST['price'],
			'#vat_type' => $_POST['vat_type'],
			'#vat' => $vat,
			'#total' => $total,
			'#net' => $net,
			'delivery_time' => $_POST['delivery_time'],
			"#status" => 1,
			"comment" => isset($_POST['comment'])?$_POST['comment']:"",
			"shipping_address" => isset($_POST['shipping_address'])?$_POST['shipping_address']:$customer['shipping_address'],
			"billing_address" => isset($_POST['billing_address'])?$_POST['billing_address']:$customer['billing_address'],
			"#rate_spot" => $_POST['rate_spot'],
			"#rate_exchange" => $_POST['rate_exchange'],
			"currency" => $_POST['currency'],
			"info_payment" => isset($_POST['payment'])?$_POST['payment']:$customer['default_payment'],
			"info_contact" => $_POST['contact'],
			"#product_id" => isset($_POST['product_id'])?$_POST['product_id']:1
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
			
			echo json_encode(array(
				'success'=>true,
				'msg'=> "Your Order ID ".$code." was created!",
				'order_id'=> $order_id 
			));

			$order = $dbc->GetRecord("bs_orders","*","id=".$order_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"order-add",$order_id,array("orders" => $order));
			
			//เมื่อมีการสร้าง Delivery Date แล้ว
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
	}

	$dbc->Close();
?>
