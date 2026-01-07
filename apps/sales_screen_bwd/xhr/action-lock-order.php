<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);
header('Content-Type: application/json');

try {
    $dbc = new dbc;
    $dbc->Connect();
    $os  = new oceanos($dbc);

    $VERSION = 'lock_v7_debug';

    if (empty($_POST['id'])) {
        throw new Exception('Order ID is required');
    }

    $order_id = intval($_POST['id']);
    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$order) {
        throw new Exception('Order not found');
    }

    $root_id = is_null($order['parent']) ? $order_id : intval($order['parent']);

    $dbc->Query("START TRANSACTION");

    $updated_orders = [];
    $clear_orders = array("#delivery_date" => "NULL");

    if ($dbc->Update("bs_orders_bwd", $clear_orders, "id=" . $root_id)) {
        $updated_orders[] = $root_id;
    }

    $child_rst = $dbc->Query("SELECT id FROM bs_orders_bwd WHERE parent=" . $root_id);
    while ($child = $dbc->Fetch($child_rst)) {
        $cid = intval($child['id']);
        if ($dbc->Update("bs_orders_bwd", $clear_orders, "id=" . $cid)) {
            $updated_orders[] = $cid;
        }
    }

    $delivery_ids = [];
    $rst_dids = $dbc->Query("
        SELECT DISTINCT delivery_id
        FROM bs_orders_bwd
        WHERE (id = {$root_id} OR parent = {$root_id})
          AND delivery_id IS NOT NULL
    ");
    while ($row = $dbc->Fetch($rst_dids)) {
        $delivery_ids[] = intval($row['delivery_id']);
    }

    $updated_deliveries = 0;
    if (!empty($delivery_ids)) {
        $delivery_ids = array_values(array_unique(array_filter($delivery_ids, fn($v) => $v > 0)));
        if (!empty($delivery_ids)) {
            $in = implode(',', $delivery_ids);
            if ($dbc->Update("bs_deliveries_bwd", array("#delivery_date" => "NULL"), "id IN (" . $in . ")")) {
                $updated_deliveries = count($delivery_ids);
            }
        }
    }

    $dbc->Query("COMMIT");

    $updated_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    $os->save_log(0, $_SESSION['auth']['user_id'] ?? 0, "order-bwd-lock", $order_id, array(
        "version" => $VERSION,
        "root_id" => $root_id,
        "cleared_orders" => $updated_orders,
        "cleared_delivery_ids" => $delivery_ids
    ));
    error_log("LockOrder called: file=" . __FILE__ . " version=" . $VERSION . " order_id=" . $order_id);

    echo json_encode(array(
        "success" => true,
        "version" => $VERSION,
        "file" => __FILE__,
        "msg" => "Lock order สำเร็จ (" . count($updated_orders) . " รายการ), เคลียร์วันส่งใน deliveries " . $updated_deliveries . " รายการ",
        "locked_orders" => $updated_orders,
        "updated_deliveries" => $delivery_ids
    ));
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode(array(
        "success" => false,
        "version" => 'lock_v7_debug',
        "file" => __FILE__,
        "msg" => "Error: " . $e->getMessage()
    ));
    error_log('Lock order error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
