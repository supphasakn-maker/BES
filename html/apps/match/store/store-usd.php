<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_mapping_usd.id",
	"mapped" => "bs_mapping_usd.mapped",
	"amount" => "bs_mapping_usd.amount",
	"spot_amount" => "(SELECT SUM(bs_mapping_usd_spots.amount) FROM bs_mapping_usd_spots WHERE bs_mapping_usd_spots.mapping_id = bs_mapping_usd.id)",
	"usd_amount" => "FORMAT((SELECT SUM(bs_mapping_usd_purchases.amount) FROM bs_mapping_usd_purchases WHERE bs_mapping_usd_purchases.mapping_id = bs_mapping_usd.id),4)",
	"remark" => "bs_mapping_usd.remark",
);

$table = array(
	"index" => "id",
	"name" => "bs_mapping_usd",
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
