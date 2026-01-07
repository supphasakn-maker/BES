<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_deliveries.id",
    "code" => "bs_deliveries.code",
    "type" => "bs_deliveries.type",
    "delivery_date" => "bs_deliveries.delivery_date",
    "created" => "bs_deliveries.created",
    "updated" => "bs_deliveries.updated",
    "status" => "bs_deliveries.status",
    "amount" => "bs_deliveries.amount",
    "user" => "bs_deliveries.user",
    "comment" => "bs_deliveries.comment",
    "order_code" => "bs_orders.code",
    "customer" => "bs_orders.customer_name",
    "customer_id" => "bs_orders.customer_id",
    "total_item" => "(SELECT COUNT(id) FROM bs_delivery_pack_items WHERE delivery_id = bs_deliveries.id)",
);

$table = array(
    "index" => "id",
    "name" => "bs_deliveries",
    "join" => array(
        array(
            "field" => "id",
            "table" => "bs_orders",
            "with" => "delivery_id"
        )
    ),
    "where" => "bs_deliveries.delivery_date = '" . $_GET['date'] . "' AND bs_orders.status > 0"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
