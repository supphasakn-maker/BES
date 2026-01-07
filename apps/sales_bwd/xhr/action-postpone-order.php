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
	
	$order = $dbc->GetRecord("bs_orders_bwd","*","id=".$_POST['id']);
	
	if($_POST['delivery_date']==""){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'Please input new delivery date?'
		));
	}else if($_POST['delivery_date']==$order['delivery_date']){
		echo json_encode(array(
			'success'=>false,
			'msg'=>'วันที่ต้องไม่เหมือนกัน'
		));
	}else{
		
		$data = array(
			"#id" => "default",
			"#order_id" => $_POST['id'],
			"delivery_date_old" => $order['delivery_date'],
			"delivery_date_new" => $_POST['delivery_date'],
			"reason_customer" => $_POST['reason_customer'],
			"reason_company" => $_POST['reason_company'],
			"#created" => "NOW()"
		);
		$dbc->Insert("bs_order_postpone_bwd",$data);

		$data = array(
			"delivery_date" => $_POST['delivery_date'],
			'#updated' => 'NOW()'
		);

		if($dbc->Update("bs_orders_bwd",$data,"id=".$_POST['id'])){
			echo json_encode(array(
				'success'=>true
			));
			$dbc->Update("bs_deliveries_bwd",$data,"id=".$order['delivery_id']);
			
			$order = $dbc->GetRecord("bs_orders_bwd","*","id=".$_POST['id']);
			$os->save_log(0,$_SESSION['auth']['user_id'],"order-bwd-postpone",$_POST['id'],array("bs_orders-bwd" => $order));
		}else{
			echo json_encode(array(
				'success'=>false,
				'msg' => "No Change"
			));
		}
	}
	

	$dbc->Close();
?>
