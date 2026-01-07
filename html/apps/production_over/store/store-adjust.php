<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_stock_adjusted_over.id",
    "type" => "bs_over_adjust_types.name",
    "product_id" => "bs_stock_adjusted_over.product_id",
    "product" => "bs_products.name",
    "remark" => "bs_stock_adjusted_over.remark",
    "code_no" => "bs_stock_adjusted_over.code_no",
    "amount" => "bs_stock_adjusted_over.amount",
    "created" => "bs_stock_adjusted_over.created",
    "updated" => "bs_stock_adjusted_over.updated",
    "ref" => "bs_stock_adjusted_over.ref",
    "date" => "bs_stock_adjusted_over.date",
);

$table = array(
    "index" => "id",
    "name" => "bs_stock_adjusted_over",
    "join" => array(
        array(
            "field" => "product_id",
            "table" => "bs_products",
            "with" => "id"

        ),
        array(
            "field" => "type_id",
            "table" => "bs_over_adjust_types",
            "with" => "id"

        )
    )
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
