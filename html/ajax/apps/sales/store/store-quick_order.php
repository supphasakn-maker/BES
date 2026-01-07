<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_quick_orders.id",
		"created" => "bs_quick_orders.created",
		"updated" => "bs_quick_orders.updated",
		"customer_id" => "bs_quick_orders.customer_id",
		"customer_name" => "bs_customers.name",
		"amount" => "bs_quick_orders.amount",
		"price" => "FORMAT(bs_quick_orders.price,2)",
		"rate_spot" => "FORMAT(bs_quick_orders.rate_spot,2)",
		"rate_exchange" => "FORMAT(bs_quick_orders.rate_exchange,2)",
		"remark" => "bs_quick_orders.remark",
		"status" => "bs_quick_orders.status",
		"order_id" => "bs_quick_orders.order_id",
		"vat_type" => "bs_quick_orders.vat_type"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_quick_orders",
		"join" => array(
			array(
				"field" => "customer_id",
				"table" => "bs_customers",
				"with" => "id"
			)
		),
		"where" => "DATE(bs_quick_orders.created) LIKE '".date("Y-m-d")."'"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
