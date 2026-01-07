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
	
	$total = 0;
	for($i=0;$i<count($_POST['totaleach']);$i++){
		$total += $_POST['totaleach'][$i];
	}
	
	if($total != $order['amount']){
		echo json_encode(array(
			'success'=>false,
			'msg'=>"จำนวนกิโลไม่ตรงกับคำสั่งซื้อ"
		));
	}else{
		
		if($dbc->HasRecord("bs_packings","order_id=".$order['id'])){
			$packing = $dbc->GetRecord("bs_packings","*","order_id=".$order['id']);
			$dbc->Delete("bs_packing_items","packing_id=".$packing['id']);
			$dbc->Delete("bs_packings","id=".$packing['id']);
		}
		
		$data = array(
			"#id" => "DEFAULT",
			"#type" => 1,
			"delivery" => $order['delivery_date'],
			"date" => date("Y-m-d"),
			"#created" => "NOW()",
			"#updated" => "NOW()",
			"#status" => 0,
			"#order_id" => $order['id'],
			"#parent" => "NULL",
			"#amount" => $order['amount'],
			"#user" => $os->auth['id'],
			"comment" => ""
		);
		
		$dbc->Insert("bs_packings",$data);
		$packing_id = $dbc->GetID();
		
		for($i=0;$i<count($_POST['name']);$i++){
			$data = array(
				"#id" => "DEFAULT",
				"name" => $_POST['name'][$i],
				"#size" => $_POST['size'][$i],
				"#amount" => $_POST['amount'][$i],
				"comment" => $_POST['comment'][$i],
				"packing_id" => $packing_id,
				"#status" => 0
			);
			$dbc->Insert("bs_packing_items",$data);
		}
		/*
		$dbc->Update("bs_quick_orders",array(
			"#status"=>2,
			"#order_id"=>$order_id
		),"id=".$_POST['id']);
		*/
		echo json_encode(array(
			'success'=>true
		));
	}

	$dbc->Close();
?>
