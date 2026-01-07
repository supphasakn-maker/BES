<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_smg_stx_other.id",
    "date" => "bs_smg_stx_other.date",
    "remark" => "bs_smg_stx_other.remark",
    "usd_debit" => "bs_smg_stx_other.usd_debit",
    "usd_credit" => "bs_smg_stx_other.usd_credit",
    "amount_debit" => "bs_smg_stx_other.amount_debit",
    "amount_credit" => "bs_smg_stx_other.amount_credit",
);

$table = array(
    "index" => "id",
    "name" => "bs_smg_stx_other"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
