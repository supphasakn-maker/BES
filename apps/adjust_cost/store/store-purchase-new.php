<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_purchase_spot.id",
	"supplier_id" => "bs_purchase_spot.supplier_id",
	"supplier" => "bs_suppliers.name",
	"type" => "bs_purchase_spot.type",

	"rate_spot" => "FORMAT(bs_purchase_spot.rate_spot,4)",
	"rate_pmdc" => "FORMAT(bs_purchase_spot.rate_pmdc,4)",
	"net_spot" => "FORMAT(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc,4)",
	"amount" => "FORMAT(bs_purchase_spot.amount,4)",
	"spot_value" => "FORMAT(bs_purchase_spot.amount*bs_purchase_spot.rate_spot*32.1507,4)",
	"spot_discount" => "FORMAT(bs_purchase_spot.amount*bs_purchase_spot.rate_pmdc,4)",
	"spot_net" => "FORMAT(bs_purchase_spot.amount*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc)*32.1507,4)",


	"date" => "bs_purchase_spot.date",
	"value_date" => "bs_purchase_spot.value_date",
	"created" => "bs_purchase_spot.created",
	"updated" => "bs_purchase_spot.updated",
	"method" => "bs_purchase_spot.method",
	"ref" => "bs_purchase_spot.ref",
	"user" => "bs_purchase_spot.user",
	"status" => "bs_purchase_spot.status",
	"comment" => "bs_purchase_spot.comment",
	"confirm" => "bs_purchase_spot.confirm",
	"order_id" => "bs_purchase_spot.order_id",
	"trade_id" => "bs_purchase_spot.trade_id",
	"import_id" => "bs_purchase_spot.import_id",
	"defer_id" => "bs_purchase_spot.defer_id",
	"parent" => "bs_purchase_spot.parent",
	"value" => "FORMAT(rate_spot*amount,4)",
);

$table = array(
	"index" => "id",
	"name" => "bs_purchase_spot",
	"join" => array(
		array(
			"field" => "supplier_id",
			"table" => "bs_suppliers",
			"with" => "id"
		)
	),
	"where" => "bs_purchase_spot.adjust_id IS NULL AND noted = 'Open-Adjust' AND bs_purchase_spot.type  != 'defer' AND bs_purchase_spot.trade_id IS NULL  AND bs_purchase_spot.defer_id IS NULL AND bs_purchase_spot.date > '2025-02-15'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
