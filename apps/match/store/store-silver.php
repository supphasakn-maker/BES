<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_mapping_silvers.id",
	"mapped" => "bs_mapping_silvers.mapped",
	"amount" => "FORMAT(bs_mapping_silvers.amount,2)",
	"order_amount" => "(SELECT SUM(bs_mapping_silver_orders.amount) FROM bs_mapping_silver_orders WHERE bs_mapping_silver_orders.mapping_id = bs_mapping_silvers.id)",
	"purchase_amount" => "FORMAT((SELECT SUM(bs_mapping_silver_purchases.amount) FROM bs_mapping_silver_purchases WHERE bs_mapping_silver_purchases.mapping_id = bs_mapping_silvers.id),4)",
	"remark" => "bs_mapping_silvers.remark",
);

$table = array(
	"index" => "id",
	"name" => "bs_mapping_silvers",
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
