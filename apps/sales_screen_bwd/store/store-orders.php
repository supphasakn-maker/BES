<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

function debug_log($message)
{
    error_log("[DataTable Debug] " . $message);
}

try {
    debug_log("Starting DataTable request");
    debug_log("GET params: " . json_encode($_GET));

    $dbc = new datastore;
    $dbc->Connect();
    debug_log("DataStore connected successfully");

    $columns = array(
        "id" => "bs_orders_bwd.id",
        "code" => "bs_orders_bwd.code",
        "customer_name" => "bs_orders_bwd.customer_name",
        "phone" => "bs_orders_bwd.phone",
        "platform" => "bs_orders_bwd.platform",
        "date" => "bs_orders_bwd.date",
        "sales" => "COALESCE(os_users.display, bs_orders_bwd.sales, 'N/A')",
        "user" => "bs_orders_bwd.user",
        "type" => "bs_orders_bwd.type",
        "parent" => "bs_orders_bwd.parent",
        "created" => "bs_orders_bwd.created",
        "updated" => "bs_orders_bwd.updated",
        "amount" => "(SELECT SUM(COALESCE(amount, 0)) FROM bs_orders_bwd sub WHERE sub.parent = bs_orders_bwd.id OR sub.id = bs_orders_bwd.id)",
        "price" => "(SELECT SUM(COALESCE(price, 0) * COALESCE(amount, 0)) FROM bs_orders_bwd sub WHERE sub.parent = bs_orders_bwd.id OR sub.id = bs_orders_bwd.id)",
        "discount_type" => "bs_orders_bwd.discount_type",
        "discount" => "bs_orders_bwd.discount",
        "net" => "(SELECT SUM(COALESCE(net, 0)) FROM bs_orders_bwd sub WHERE sub.parent = bs_orders_bwd.id OR sub.id = bs_orders_bwd.id)",
        "total" => "bs_orders_bwd.total",
        "delivery_date" => "bs_orders_bwd.delivery_date",
        "delivery_time" => "bs_orders_bwd.delivery_time",
        "lock_status" => "bs_orders_bwd.lock_status",
        "status" => "bs_orders_bwd.status",
        "shipping_address" => "bs_orders_bwd.shipping_address",
        "billing_address" => "bs_orders_bwd.billing_address",
        "shipping" => "bs_orders_bwd.shipping",
        "engrave" => "bs_orders_bwd.engrave",
        "font" => "bs_orders_bwd.font",
        "carving" => "bs_orders_bwd.carving",
        "billing_id" => "bs_orders_bwd.billing_id",
        "default_bank" => "bs_orders_bwd.default_bank",
        "info_payment" => "bs_orders_bwd.info_payment",
        "info_contact" => "bs_orders_bwd.info_contact",
        "delivery_id" => "bs_orders_bwd.delivery_id",
        "remove_reason" => "bs_orders_bwd.remove_reason",
        "product_type" => "bs_orders_bwd.product_type",
        "product_id" => "bs_orders_bwd.product_id",
        "delivery_code" => "COALESCE(bs_deliveries_bwd.code, '')",
        "item_count" => "(SELECT COUNT(*) FROM bs_orders_bwd sub WHERE sub.parent = bs_orders_bwd.id OR sub.id = bs_orders_bwd.id)",
        "box_count" => "(SELECT COUNT(DISTINCT sub.box_number) 
                 FROM bs_orders_bwd sub 
                 WHERE sub.parent = bs_orders_bwd.id 
                    OR sub.id = bs_orders_bwd.id)",

    );

    $table = array(
        "index" => "id",
        "name" => "bs_orders_bwd",
        "join" => array(
            array(
                "field" => "sales",
                "table" => "os_users",
                "with" => "id",
                "type" => "LEFT"
            ),
            array(
                "field" => "delivery_id",
                "table" => "bs_deliveries_bwd",
                "with" => "id",
                "type" => "LEFT"
            )
        ),
        "where" => "DATE(bs_orders_bwd.created) = '" . date("Y-m-d") . "' AND bs_orders_bwd.status > 0 AND bs_orders_bwd.parent IS NULL"
    );

    debug_log("Table config: " . json_encode($table));

    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $order = isset($_GET['order']) ? $_GET['order'] : array();
    $columns_param = isset($_GET['columns']) ? $_GET['columns'] : array();
    $search = isset($_GET['search']) ? $_GET['search'] : array('value' => '');

    debug_log("Processing with draw: $draw, start: $start, length: $length");

    $dbc->SetParam($table, $columns, $order, $columns_param, $search);
    $dbc->SetLimit($length, $start);
    $dbc->Processing();

    $result = $dbc->GetResult();
    debug_log("Raw result: " . json_encode($result));

    if ($result && is_array($result)) {
        $formatted_result = array(
            "draw" => $draw,
            "recordsTotal" => isset($result['iTotalRecords']) ? intval($result['iTotalRecords']) : 0,
            "recordsFiltered" => isset($result['iTotalDisplayRecords']) ? intval($result['iTotalDisplayRecords']) : 0,
            "data" => isset($result['aaData']) ? $result['aaData'] : array()
        );

        debug_log("Formatted result - Total: " . $formatted_result['recordsTotal'] .
            ", Filtered: " . $formatted_result['recordsFiltered'] .
            ", Data count: " . count($formatted_result['data']));

        $result = $formatted_result;
    } else {
        debug_log("Invalid result, creating empty response");
        $result = array(
            "draw" => $draw,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => array()
        );
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    debug_log("Exception: " . $e->getMessage());
    debug_log("Stack trace: " . $e->getTraceAsString());

    $error_response = array(
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => array(),
        "error" => $e->getMessage()
    );

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
