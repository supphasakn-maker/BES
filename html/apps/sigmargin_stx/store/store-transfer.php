<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_smg_stx_payment.id",
    "date" => "bs_smg_stx_payment.date",
    "type" => "bs_smg_stx_payment.type",
    "amount_usd" => "bs_smg_stx_payment.amount_usd",
    "rate_pmdc" => "bs_smg_stx_payment.rate_pmdc",
    "amount_total" => "bs_smg_stx_payment.amount_total"
);

$table = array(
    "index" => "id",
    "name" => "bs_smg_stx_payment",
    "where" => "bs_smg_stx_payment.date LIKE '" . $_GET['date'] . "'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
