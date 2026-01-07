<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_orders.id",
		"code" => "bs_orders.code",
		"customer_id" => "bs_orders.customer_id",
		"customer_name" => "bs_orders.customer_name",
		"date" => "DATE(bs_orders.date)",
		"sales" => "bs_orders.sales",
		"user" => "bs_orders.user",
		"type" => "bs_orders.type",
		"parent" => "bs_orders.parent",
		"created" => "bs_orders.created",
		"updated" => "bs_orders.updated",
		"amount" => "bs_orders.amount",
		"price" => "FORMAT(bs_orders.price,0)",
		"vat_type" => "bs_orders.vat_type",
		"vat" => "bs_orders.vat",
		"total" => "FORMAT(bs_orders.total,0)",
		"net" => "bs_orders.net",
		"delivery_date" => "bs_orders.delivery_date",
		"delivery_time" => "bs_orders.delivery_time",
		"lock_status" => "bs_orders.lock_status",
		"status" => "bs_orders.status",
		"comment" => "bs_orders.comment",
		"shipping_address" => "bs_orders.shipping_address",
		"billing_address" => "bs_orders.billing_address",
		"rate_spot" => "bs_orders.rate_spot",
		"rate_exchange" => "bs_orders.rate_exchange",
		"billing_id" => "bs_orders.billing_id",
		"currency" => "bs_orders.currency",
		"info_payment" => "bs_orders.info_payment",
		"info_contact" => "bs_orders.info_contact",
		"delivery_id" => "bs_orders.delivery_id",
		"remove_reason" => "bs_orders.remove_reason",
		"product_id" => "bs_orders.product_id",
	);
	
	$where = "bs_mapping_silver_orders.id IS NULL AND bs_orders.status > 0  AND YEAR(date) > 2021";
	if(isset($_GET['date_filter'])){
		$where .= " AND DATE(bs_orders.date) = '".$_GET['date_filter']."'";
	}

	$table = array(
		"index" => "id",
		"name" => "bs_orders",
		"join" => array(
			array(
				"field" => "id",
				"table" => "bs_mapping_silver_orders",
				"with" => "order_id",
			)
		
		),"where" => $where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	
	$total_remain = 0;
	
	$aaData = array();
	$data = $dbc->GetResult();
	$sql = "SELECT * FROM bs_mapping_silver_orders WHERE mapping_id IS NULL";
	
	$rst = $dbc->Query($sql);
	while($mapping = $dbc->Fetch($rst)){
		$order = $dbc->GetRecord("bs_orders","*","id=".$mapping['order_id']);
		$total = $order['amount']*$order['rate_spot'];
		
		array_push($aaData,array(
			"DT_RowId" => $order['id'],
			"id" => $order['id'],
			"code" =>  $order['code'],
			"customer_id" =>  $order['customer_id'],
			"customer_name" =>  $order['customer_name'],
			"date" => $order['date'],
			"sales" =>  $order['sales'],
			"user" =>  $order['user'],
			"type" =>  $order['type'],
			"parent" =>  $order['parent'],
			"amount" =>  $order['amount'],
			"price" => number_format($order['price'],0),
			"vat" =>  $order['vat'],
			"total" =>  number_format($order['total'],0),
			"net" =>  $order['net'],
			"rate_spot" =>  $order['rate_spot'],
			"rate_exchange" =>  $order['rate_exchange'],
			"remain" => $mapping['amount'],
			"mapping_item_id" => $mapping['id']
		));
		
		$total_remain += $mapping['amount'];
	}
	
	for($i=0;$i<count($data['aaData']);$i++){
		array_push($aaData,$data['aaData'][$i]);
	}
	
	$data['aaData'] = $aaData;
	
	$sql = "SELECT SUM(bs_orders.amount) FROM bs_orders 
	LEFT JOIN bs_mapping_silver_orders ON bs_mapping_silver_orders.order_id = bs_orders.id 
	WHERE bs_mapping_silver_orders.id IS NULL AND bs_orders.status > 0";
	
	
	if(isset($_GET['date_filter'])){
		$sql .= " AND DATE(bs_orders.date) = '".$_GET['date_filter']."'";
	}
	
	$rst = $dbc->Query($sql);
	$line = $dbc->Fetch($rst);
	
	
	$data['total'] = array(
		"remian_unmatch" => $line[0],
		"remain_matching" => $total_remain,
		"remain_total" => $line[0]+$total_remain
	);
	
	
	
	echo json_encode($data);

	$dbc->Close();

?>
