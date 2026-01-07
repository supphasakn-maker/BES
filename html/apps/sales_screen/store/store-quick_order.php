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
	"sales" => "bs_employees.fullname",
	"amount" => "bs_orders.amount",
	"price" => "FORMAT(bs_orders.price,2)",
	"total" => "FORMAT(bs_orders.total,2)",
	"usd" => "FORMAT(bs_orders.usd,2)",

	"rate_spot" => "FORMAT(bs_quick_orders.rate_spot,2)",
	"rate_exchange" => "FORMAT(bs_quick_orders.rate_exchange,2)",
	"remark" => "bs_quick_orders.remark",
	"status" => "bs_quick_orders.status",
	"order_id" => "bs_quick_orders.order_id",
	"vat_type" => "bs_quick_orders.vat_type",
	"product" => "bs_products.name",
	"delivery_date" => "bs_orders.delivery_date",
	"code" => "bs_orders.code"
);

$table = array(
	"index" => "id",
	"name" => "bs_quick_orders",
	"join" => array(
		array(
			"field" => "customer_id",
			"table" => "bs_customers",
			"with" => "id"
		),
		array(
			"join" => "bs_customers",
			"field" => "default_sales",
			"table" => "bs_employees",
			"with" => "id"
		),
		array(
			"field" => "sales",
			"table" => "os_users",
			"with" => "id"
		),
		array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		),
		array(
			"field" => "order_id",
			"table" => "bs_orders",
			"with" => "id"
		)
	),
	"where" => "DATE(bs_quick_orders.created) LIKE '" . date("Y-m-d") . "' AND (bs_orders.status > -1 OR bs_orders.id IS NULL) AND bs_quick_orders.product_id != '2'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
