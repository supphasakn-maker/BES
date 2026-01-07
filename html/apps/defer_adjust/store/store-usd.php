<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id"             => "bs_match_usd.id",
    "bank"           => "bs_match_usd.bank",
    "bankname"         => "bs_banks.name",
    "date"         => "bs_match_usd.date",
    "usd"         => "bs_match_usd.usd",
    "comment"         => "bs_match_usd.comment"

);

$table = array(
    "index" => "id",
    "name" => "bs_match_usd",
    "join" => array(
        array(
            "field" => "bank",
            "table" => "bs_banks",
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
