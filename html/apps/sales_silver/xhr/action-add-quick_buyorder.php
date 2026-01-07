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
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");


	if($_POST['customer_id'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุลูกค้า'
		));
	}else if($_POST['amount'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุจำนวน'
		));
	}else if($_POST['price'] == ""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'โปรดระบุราคา'
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
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#customer_id" => $_POST['customer_id'],
			"#amount" => $_POST['amount'],
			"#price" => $_POST['price'],
			"#vat_type" => $_POST['vat_type'],
			"#vat" => $vat,
			"#total" => $total,
			"#net" => $net,
			"#rate_spot" => $_POST['rate_spot']!=""?$_POST['rate_spot']:$rate_spot,
			"#rate_exchange" => $_POST['rate_exchange']!=""?$_POST['rate_exchange']:$rate_spot,
			"remark" => $_POST['remark'],
			"#status" => 1,
			"#order_id" => "NULL",
			"#vat_type" => $_POST['vat_type'],
			"#sales" => isset($_POST['sales'])?$_POST['sales']:(is_null($customer['default_sales'])?"NULL":$customer['default_sales']),
			"#product_id" => $_POST['product_id']
			
			
		);

		if($dbc->Insert("bs_orders_buy",$data)){
			$quick_order_id = $dbc->GetID();
			echo json_encode(array(
				'success'=>true,
				'msg'=> $quick_order_id
			));
			$code = "BF-".sprintf("%07s", $quick_order_id);
			$dbc->Update("bs_orders_buy",array("code"=>$code),"id=".$quick_order_id);

			$quick_order = $dbc->GetRecord("bs_orders_buy","*","id=".$quick_order_id);
			$os->save_log(0,$_SESSION['auth']['user_id'],"bs_quick_orders_buy-add",$quick_order_id,array("quick_orders_buy" => $quick_order));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "Insert Error"
			));
		}
	}

	$dbc->Close();
?>
