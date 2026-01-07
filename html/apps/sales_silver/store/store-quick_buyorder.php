<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_orders_buy.id",
		"code" => "bs_orders_buy.code",
		"created" => "bs_orders_buy.created",
		"updated" => "bs_orders_buy.updated",
		"customer_id" => "bs_orders_buy.customer_id",
		"customer_name" => "bs_customers.name",
		"sales" => "bs_employees.fullname",
		"amount" => "bs_orders_buy.amount",
		"price" => "FORMAT(bs_orders_buy.price,2)",
		"rate_spot" => "FORMAT(bs_orders_buy.rate_spot,2)",
		"rate_exchange" => "FORMAT(bs_orders_buy.rate_exchange,2)",
		"remark" => "bs_orders_buy.remark",
		"status" => "bs_orders_buy.status",
		"order_id" => "bs_orders_buy.order_id",
		"vat_type" => "bs_orders_buy.vat_type",
		"product" => "bs_products.name",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_orders_buy",
		"join" => array(
			array(
				"field" => "customer_id",
				"table" => "bs_customers",
				"with" => "id"
			),array(
				"join" => "bs_customers",
				"field" => "default_sales",
				"table" => "bs_employees",
				"with" => "id"
			),array(
				"field" => "sales",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "product_id",
				"table" => "bs_products",
				"with" => "id"
			)
		),
		"where" => "DATE(bs_orders_buy.created) LIKE '".date("Y-m-d")."' AND bs_orders_buy.product_id = '2'"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
