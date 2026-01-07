<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_orders_back_bwd.id",
    "code" => "bs_orders_back_bwd.code",
    "customer_name" => "bs_orders_back_bwd.customer_name",
    "phone" => "bs_orders_back_bwd.phone",
    "platform" => "bs_orders_back_bwd.platform",
    "date" => "bs_orders_back_bwd.date",
    "sales" => "os_users.display",
    "user" => "bs_orders_back_bwd.user",
    "type" => "bs_orders_back_bwd.type",
    "parent" => "bs_orders_back_bwd.parent",
    "created" => "bs_orders_back_bwd.created",
    "updated" => "bs_orders_back_bwd.updated",
    "amount" => "FORMAT(bs_orders_back_bwd.amount,4)",
    "price" => "FORMAT(bs_orders_back_bwd.price,2)",
    "net" => "FORMAT(bs_orders_back_bwd.net,2)",
    "total" => "FORMAT(bs_orders_back_bwd.total,2)",
    "comment" => "bs_orders_back_bwd.comment",
    "status" => "bs_orders_back_bwd.status",
    "engrave" => "bs_orders_back_bwd.engrave",
    "remove_reason" => "bs_orders_back_bwd.remove_reason",
    "product_type" => "bs_orders_back_bwd.product_type",
    "product_id" => "bs_orders_back_bwd.product_id"
);

$table = array(
    "index" => "id",
    "name" => "bs_orders_back_bwd",
    "join" => array(
        array(
            "field" => "sales",
            "table" => "os_users",
            "with" => "id"
        )
    ),
    "where" => "DATE(bs_orders_back_bwd.created) LIKE '" . date("Y-m-d") . "' AND (bs_orders_back_bwd.status > 0)"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
