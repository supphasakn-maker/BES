<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_productions_switch.id",
    "round" => "bs_productions_switch.round",
    "round_turn" => "bs_productions_switch.round_turn",
    "created" => "bs_productions_switch.created",
    "updated" => "bs_productions_switch.updated",
    "user" => "bs_productions_switch.user",
    "remark" => "bs_productions_switch.remark",
    "weight_out_packing" => "bs_productions_switch.weight_out_packing",
    "weight_out_total" => "bs_productions_switch.weight_out_total",
    "submited" => "bs_productions_switch.submited",
    "date_back" => "bs_productions_switch.date_back",
    "product_name" => "bs_products.name",
    "product_name_turn" => "bs_products_turn.name",
    "status" => "bs_productions_switch.status"
);

$where = "1";

if (isset($_GET['date_from'])) {
    $where .= " AND date(bs_productions_switch.created) BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "'";
}

$table = array(
    "index" => "id",
    "name" => "bs_productions_switch",
    "join" => array(
        array(
            "field" => "product_id",
            "table" => "bs_products",
            "with" => "id"
        ),
        array(
            "field" => "product_id_turn",
            "table" => "bs_products_turn",
            "with" => "id"
        )
    ),
    "where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
