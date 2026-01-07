<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_smg_stx_daily.id",
    "date" => "bs_smg_stx_daily.date",
    "spot_sell" => "bs_smg_stx_daily.spot_sell",
    "spot_buy" => "bs_smg_stx_daily.spot_buy",
    "cash" => "bs_smg_stx_daily.cash",
);

$table = array(
    "index" => "id",
    "name" => "bs_smg_stx_daily",
    //"where" => "bs_smg_stx_daily.date LIKE '".$_GET['date']."'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
