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
		"code" => "bs_deliveries.code",
		"order_code" => "bs_orders.code",
		"order_id" => "bs_orders.id",
		"delivery_id" => "bs_deliveries.id",
		"billing_id" => "bs_deliveries.billing_id",
		"type" => "bs_deliveries.type",
		"delivery_date" => "bs_deliveries.delivery_date",
		"created" => "bs_deliveries.created",
		"updated" => "bs_deliveries.updated",
		"status" => "bs_deliveries.status",
		"amount" => "FORMAT(bs_deliveries.amount,4)",
		"user" => "bs_deliveries.user",
		"comment" => "bs_deliveries.comment",
		"customer_name" => "bs_orders.customer_name",
		"date" => "bs_orders.date",
		"info_payment" => "bs_orders.info_payment",
		"vat_amount" => "FORMAT(bs_orders.vat,2)",
		"total" => "FORMAT(bs_orders.total,2)",
		"net" => "FORMAT(bs_orders.net,2)",
		"payment_status" => "bs_payments.status",
		"payment_id" => "bs_payments.id"
	);

	$where = '
		bs_orders.status > 0
	';
	if(isset($_GET['date_from'])){
		
		$where .= " AND (bs_deliveries.delivery_date BETWEEN '".$_GET['date_from']." 00:00:00' AND '".$_GET['date_to']." 23:59:59')";
		
	}
	
	if($_GET['show']=="false"){
		$where .= " AND bs_payments.id IS NULL";
	}
	
	$table = array(
		"index" => "id",
		"name" => "bs_orders",
		"join" => array(
			array(
				"field" => "delivery_id",
				"table" => "bs_deliveries",
				"with" => "id"
			),array(
				"field" => "id",
				"table" => "bs_payment_orders",
				"with" => "order_id"
			),array(
				"join" => "bs_payment_orders",
				"field" => "payment_id",
				"table" => "bs_payments",
				"with" => "id"
			)
		),
		"where" => $where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
