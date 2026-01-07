<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_switch_pack_items.id",
    "switch_id" => "bs_switch_pack_items.switch_id",
    "item_type" => "bs_switch_pack_items.item_type",
    "item_id" => "bs_switch_pack_items.item_id",
    "remark" => "bs_switch_pack_items.remark",
    "mapping" => "bs_switch_pack_items.mapping",
    "status" => "bs_switch_pack_items.status",
    "code" => "bs_packing_items.code",
    "pack_name" => "bs_packing_items.pack_name",
    "pack_type" => "bs_packing_items.pack_type",
    "weight_actual" => "bs_packing_items.weight_actual",
    "weight_expected" => "bs_packing_items.weight_expected"
);

$table = array(
    "index" => "id",
    "name" => "bs_switch_pack_items",
    "join" => array(
        array(
            "field" => "item_id",
            "table" => "bs_packing_items",
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
