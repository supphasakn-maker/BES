<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_adjust_cost.id",
	"date_adjust" => "bs_adjust_cost.date_adjust",
	"created" => "bs_adjust_cost.created",
	"updated" => "bs_adjust_cost.updated",
	"value_amount" => "bs_adjust_cost.value_amount",
	"value_buy" => "FORMAT(bs_adjust_cost.value_buy,2)",
	"value_sell" => "FORMAT(bs_adjust_cost.value_sell,2)",
	"value_new" => "FORMAT(bs_adjust_cost.value_new,2)",
	"value_profit" => "FORMAT(bs_adjust_cost.value_profit,2)",
	"value_adjust_cost" => "FORMAT(bs_adjust_cost.value_adjust_cost,2)",
	"value_adjust_discount" => "FORMAT(bs_adjust_cost.value_adjust_discount,2)",
	"value_net" => "FORMAT(bs_adjust_cost.value_net,2)",
	"user" => "bs_adjust_cost.user",
	"adjust_id" => "bs_purchase_spot.adjust_id",
	"adjust_type" => "bs_purchase_spot.adjust_type",
	"supplier_id" => "bs_purchase_spot.supplier_id",
	"supplier" => "bs_suppliers.name",
	"product_id" => "bs_purchase_spot.product_id",
	"products" => "bs_products.name",
);

$table = array(
	"index" => "id",
	"name" => "bs_purchase_spot",
	"join" => array(
		array(
			"field" => "adjust_id",
			"table" => "bs_adjust_cost",
			"with" => "id"
		),
		array(
			"field" => "supplier_id",
			"table" => "bs_suppliers",
			"with" => "id"
		),
		array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		)
	),
	"where" => "bs_purchase_spot.adjust_type = 'new'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
