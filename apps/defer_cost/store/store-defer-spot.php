<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_incoming_plans.id",
    "supplier_id" => "bs_incoming_plans.supplier_id",
    "supplier_name" => "bs_suppliers.name",
    "amount" => "FORMAT(bs_incoming_plans.amount,4)",
    "usd" => "FORMAT(bs_incoming_plans.usd,4)",
    "import_date" => "bs_incoming_plans.import_date",
    "created" => "bs_incoming_plans.created",
    "updated" => "bs_incoming_plans.updated",
    "product_type_id" => "bs_incoming_plans.product_type_id",
    "product_name" => "bs_products.name",
    "user" => "os_users.display",
    "status" => "bs_incoming_plans.status",
    "defer_id" => "bs_incoming_plans.defer_id",
);

$table = array(
    "index" => "id",
    "name" => "bs_incoming_plans",
    "join" => array(
        array(
            "field" => "supplier_id",
            "table" => "bs_suppliers",
            "with" => "id"
        ),
        array(
            "field" => "user_id",
            "table" => "os_users",
            "with" => "id"
        ),
        array(
            "field" => "product_type_id",
            "table" => "bs_products",
            "with" => "id"
        )
    ),
    "where" => "bs_incoming_plans.defer_id IS NULL AND bs_incoming_plans.usd IS NOT NULL AND bs_incoming_plans.supplier_id  IN (1,6)  "
);


$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
