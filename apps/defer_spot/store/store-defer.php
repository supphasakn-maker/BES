<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_defer_spot.id",
    "supplier_id" => "bs_defer_spot.supplier_id",
    "supplier_name" => "bs_suppliers.name",
    "amount" => "FORMAT(bs_defer_spot.amount,4)",
    "price" => "FORMAT(bs_defer_spot.price,4)",
    "rate_spot" => "bs_defer_spot.rate_spot",
    "rate_pmdc" => "bs_defer_spot.rate_pmdc",
    "value_date" => "bs_defer_spot.value_date",
    "created" => "bs_defer_spot.created",
    "updated" => "bs_defer_spot.updated",
    "ref" => "bs_defer_spot.ref",
    "user" => "os_users.display",
    "status" => "bs_defer_spot.status",
    "defer_id" => "bs_defer_spot.defer_id",
);

$table = array(
    "index" => "id",
    "name" => "bs_defer_spot",
    "join" => array(
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
            "with" => "id"
        ),
        array(
            "field" => "user",
            "table" => "os_users",
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
