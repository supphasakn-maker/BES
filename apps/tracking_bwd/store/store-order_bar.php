<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
    "id" => "bs_orders.id",
    "code" => "bs_orders.code",
    "customer_id" => "bs_orders.customer_id",
    "customer_name" => "bs_orders.customer_name",
    "date" => "bs_orders.date",
    "sales" => "bs_employees.fullname",
    "user" => "bs_orders.user",
    "type" => "bs_orders.type",
    "parent" => "bs_orders.parent",
    "created" => "bs_orders.created",
    "updated" => "bs_orders.updated",
    "amount" => "FORMAT(bs_orders.amount,4)",
    "price" => "FORMAT(bs_orders.price,2)",
    "vat_type" => "bs_orders.vat_type",
    "vat" => "FORMAT(bs_orders.vat,2)",
    "total" => "FORMAT(bs_orders.total,2)",
    "net" => "FORMAT(bs_orders.net,2)",
    "delivery_date" => "bs_orders.delivery_date",
    "delivery_time" => "bs_orders.delivery_time",
    "lock_status" => "bs_orders.lock_status",
    "status" => "bs_orders.status",
    "comment" => "bs_orders.comment",
    "shipping_address" => "bs_orders.shipping_address",
    "billing_address" => "bs_orders.billing_address",
    "rate_spot" => "bs_orders.rate_spot",
    "rate_exchange" => "bs_orders.rate_exchange",
    "billing_id" => "bs_orders.billing_id",
    "currency" => "bs_orders.currency",
    "info_payment" => "bs_orders.info_payment",
    "info_contact" => "bs_orders.info_contact",
    "delivery_id" => "bs_orders.delivery_id",
    "delivery_code" => "bs_deliveries.code",
    "product_id" => "bs_orders.product_id",
    "Tracking" => "bs_orders.Tracking",
    "orderable_type" => "bs_orders.orderable_type"
);

$where = '';

$validDate = function ($s) {
    return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);
};


$from = $_GET['from']       ?? $_GET['date_from'] ?? null;
$to   = $_GET['to']         ?? $_GET['date_to']   ?? null;

if ($from && !$to) {
    $to = $from;
}

if (isset($_GET['combine_mode'])) {
    $where .= " AND bs_orders.delivery_id IS NULL";
}

if ($from && $to && $validDate($from) && $validDate($to)) {
    $where .= " AND bs_orders.delivery_date BETWEEN '{$from}' AND '{$to}'";
}

if (isset($_GET['delivery_date']) && $validDate($_GET['delivery_date'])) {
    $d = $_GET['delivery_date'];
    $where .= " AND bs_orders.delivery_date = '{$d}'";
}

if (isset($_GET['customer_id']) && is_numeric($_GET['customer_id'])) {
    $where .= " AND bs_orders.customer_id = " . $_GET['customer_id'];
}



$table = array(
    "index" => "id",
    "name"  => "bs_orders",
    "join"  => array(
        array("field" => "user",        "table" => "os_users",     "with" => "id"),
        array("field" => "sales",       "table" => "bs_employees", "with" => "id"),
        array("field" => "delivery_id", "table" => "bs_deliveries", "with" => "id")
    ),
    "where" => "bs_orders.status > 0 AND bs_orders.product_id = 2" . $where
);

// ป้องกัน notice ถ้า key ไม่ถูกส่งมา
$dbc->SetParam(
    $table,
    $columns,
    $_GET['order']   ?? [],
    $_GET['columns'] ?? [],
    $_GET['search']  ?? []
);

$length = isset($_GET['length']) ? (int)$_GET['length'] : 25;
$start  = isset($_GET['start'])  ? (int)$_GET['start']  : 0;

$dbc->SetLimit($length, $start);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
