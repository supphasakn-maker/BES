<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_productions_silver_save.id",
    "date" => "bs_productions_silver_save.date",
    "bar" => "bs_productions_silver_save.bar",
    "amount" => "bs_productions_silver_save.amount",
    "time" => "bs_productions_silver_save.time",
    "user" => "bs_productions_silver_save.user",
    "status" => "bs_productions_silver_save.status",
);


$table = array(
    "index" => "id",
    "name" => "bs_productions_silver_save",
    "where" => "bs_productions_silver_save.round = " . $_GET['production_id']
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
