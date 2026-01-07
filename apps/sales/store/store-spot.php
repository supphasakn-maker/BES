<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_sales_spot.id",
	"type" => "bs_sales_spot.type",
	"supplier_id" => "bs_sales_spot.supplier_id",
	"supplier_name" => "bs_suppliers.name",
	"amount" => "bs_sales_spot.amount",
	"rate_spot" => "bs_sales_spot.rate_spot",
	"rate_pmdc" => "bs_sales_spot.rate_pmdc",
	"date" => "bs_sales_spot.date",
	"value_date" => "bs_sales_spot.value_date",
	"created" => "bs_sales_spot.created",
	"updated" => "bs_sales_spot.updated",
	"method" => "bs_sales_spot.method",
	"ref" => "bs_sales_spot.ref",
	"user" => "os_users.name",
	"status" => "bs_sales_spot.status",
	"comment" => "bs_sales_spot.comment",
	"name_sup" => "bs_suppliers_mapping.name",
	"trade_id" => "bs_sales_spot.trade_id",
	"product_id"    => "bs_sales_spot.product_id",
	"name"    		=> "bs_products.name",
	"maturity"    		=> "bs_sales_spot.maturity",
);

$table = array(
	"index" => "id",
	"name" => "bs_sales_spot",
	"join" => array(
		array(
			"field" => "supplier_id",
			"table" => "bs_suppliers",
			"with" => "id"
		),
		array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		),
		array(
			"field" => "ref",
			"table" => "bs_suppliers_mapping",
			"with" => "id"
		),
		array(
			"field" => "user",
			"table" => "os_users",
			"with" => "id"
		)
	)
);

if (isset($_GET['where'])) {
	$table['where'] = $_GET['where'];
}

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
