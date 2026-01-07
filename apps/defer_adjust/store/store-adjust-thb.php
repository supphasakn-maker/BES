<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id"             => "bs_adjust_thb.id",
    "supplier_id"    => "bs_adjust_thb.supplier_id",
    "supplier"         => "bs_adjust_thb.name",
    "date"         => "bs_adjust_thb.date",
    "amount"         => "bs_adjust_thb.amount",
    "usd"         => "bs_adjust_thb.usd",

);

$table = array(
    "index" => "id",
    "name" => "bs_adjust_thb",
    "join" => array(
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
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
