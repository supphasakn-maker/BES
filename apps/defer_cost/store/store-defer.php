<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_defer_cost.id",
    "date_defer" => "bs_defer_cost.date_defer",
    "created" => "bs_defer_cost.created",
    "updated" => "bs_defer_cost.updated",
    "amount" => "bs_incoming_plans.amount",
    "value_defer_spot" => "FORMAT(bs_defer_cost.value_defer_spot,2)",
    "value_net" => "FORMAT(bs_defer_cost.value_net,2)",
    "defer" => "FORMAT(bs_defer_cost.defer,2)",
    "supplier_id" => "bs_defer_cost.supplier_id",
    "user" => "bs_defer_cost.user",
    "name" => "bs_suppliers.name"
);

$table = array(
    "index" => "id",
    "name" => "bs_defer_cost",
    "join" => array(
        array(
            "field" => "id",
            "table" => "bs_incoming_plans",
            "with" => "defer_id"
        ),
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
            "with" => "id"
        )
    ),
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
