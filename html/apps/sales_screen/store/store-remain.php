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
		"date" => "bs_orders.date",
		"sales" => "bs_employees.fullname",
		"user" => "bs_orders.user",
		"type" => "bs_orders.type",
		"parent" => "bs_orders.parent",
		"created" => "bs_orders.created",
		"updated" => "bs_orders.updated",
		"amount" => "FORMAT(bs_orders.amount,4)",
		"price" => "FORMAT(bs_orders.price,2)",
		"vat_type" => "bs_orders.vat_type",
		"vat" => "FORMAT(bs_orders.vat,2)",
		"total" => "FORMAT(bs_orders.total,2)",
		"net" => "FORMAT(bs_orders.net,2)",
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
		"delivery_code" => "bs_deliveries.code",
		"amount_value" => "bs_orders.amount",
	);

	$where = '';
	
	$where .= " AND (bs_orders.delivery_date >= '".date("Y-m-d")."' OR bs_orders.delivery_date IS NULL)";
	
	if(isset($_GET['delivery_date'])){
		$where .= " AND bs_orders.delivery_date = '".$_GET['delivery_date']."'";
	}
	
	if(isset($_GET['customer_id'])){
		$where .= " AND bs_orders.customer_id = ".$_GET['customer_id'];
	}

	$table = array(
		"index" => "id",
		"name" => "bs_orders",
		"join" => array(
			array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "sales",
				"table" => "bs_employees",
				"with" => "id"
			),array(
				"field" => "delivery_id",
				"table" => "bs_deliveries",
				"with" => "id"
			)
		),
		"where" => "bs_orders.status > 0".$where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
