<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_transfers.id",
	"bank" => "bs_transfers.bank",
	"date" => "bs_transfers.date",
	"type" => "bs_transfers.type",
	"supplier_id" => "bs_transfers.supplier_id",
	"value_usd_goods" => "FORMAT(bs_transfers.value_usd_goods,4)",
	"value_usd_deposit" => "bs_transfers.value_usd_deposit",
	"value_usd_paid" => "bs_transfers.value_usd_paid",
	"value_usd_adjusted" => "bs_transfers.value_usd_adjusted",
	"value_usd_total" => "bs_transfers.value_usd_total",
	"value_usd_fixed" => "bs_transfers.value_usd_fixed",
	"value_usd_nonfixed" => "bs_transfers.value_usd_nonfixed",
	"rate_counter" => "bs_transfers.rate_counter",
	"value_thb_fixed" => "bs_transfers.value_thb_fixed",
	"value_thb_premium" => "bs_transfers.value_thb_premium",
	"value_thb_net" => "bs_transfers.value_thb_net",
	"created" => "bs_transfers.created",
	"updated" => "bs_transfers.updated",
	"remark" => "bs_transfers.remark",
	"value_thb_transaction" => "bs_transfers.value_thb_transaction",
	"paid_thb" => "bs_transfers.paid_thb",
	"paid_usd" => "bs_transfers.paid_usd",
	"status" => "bs_transfers.status",
	"amount" => "bs_transfers.amount",
	"value_adjust_trade" => "bs_transfers.value_adjust_trade",
	"value_edit_trade" => "bs_transfers.value_edit_trade",
	"supplier" => "bs_suppliers.name",
	"product_id" => "bs_transfers.product_id",
	"interest_match" => "bs_transfers.interest_match",
	"name" => "bs_products.name",

);

$table = array(
	"index" => "id",
	"name" => "bs_transfers",
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
		)
	)
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
