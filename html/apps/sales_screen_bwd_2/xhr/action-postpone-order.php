<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

// เพิ่ม JSON header
header('Content-Type: application/json');

try {
    $dbc = new dbc;
    $dbc->Connect();
    $os = new oceanos($dbc);

    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Order ID is required');
    }

    $order_id = intval($_POST['id']);
    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

    if (!$order) {
        throw new Exception('Order not found');
    }

    if (empty($_POST['delivery_date'])) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Please input new delivery date?'
        ));
        exit;
    }

    if ($_POST['delivery_date'] == $order['delivery_date']) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'วันที่ต้องไม่เหมือนกัน'
        ));
        exit;
    }

    // บันทึก postpone log
    $postpone_data = array(
        "#id" => "default",
        "#order_id" => $order_id,
        "delivery_date_old" => $order['delivery_date'],
        "delivery_date_new" => $_POST['delivery_date'],
        "reason_customer" => $_POST['reason_customer'] ?? '',
        "reason_company" => $_POST['reason_company'] ?? '',
        "#created" => "NOW()"
    );
    $dbc->Insert("bs_order_postpone_bwd", $postpone_data);

    // ข้อมูลสำหรับอัปเดต
    $update_data = array(
        "delivery_date" => $_POST['delivery_date'],
        '#updated' => 'NOW()'
    );

    // กำหนดว่าเป็นออเดอร์หลักหรือลูก
    $is_parent = is_null($order['parent']);
    $updated_orders = [];

    if ($is_parent) {
        // กรณีออเดอร์หลัก - อัปเดตทั้งหลักและลูก

        // อัปเดตออเดอร์หลัก
        $result = $dbc->Update("bs_orders_bwd", $update_data, "id=" . $order_id);
        if ($result) {
            $updated_orders[] = $order_id;
        }

        // อัปเดตออเดอร์ลูกทั้งหมด
        $child_sql = "SELECT id FROM bs_orders_bwd WHERE parent = " . $order_id;
        $child_rst = $dbc->Query($child_sql);

        while ($child = $dbc->Fetch($child_rst)) {
            $child_result = $dbc->Update("bs_orders_bwd", $update_data, "id=" . $child['id']);
            if ($child_result) {
                $updated_orders[] = $child['id'];
            }
        }

        // อัปเดต delivery record
        if ($order['delivery_id']) {
            $dbc->Update("bs_deliveries_bwd", $update_data, "id=" . $order['delivery_id']);
        }
    } else {
        // กรณีออเดอร์ลูก - อัปเดตเฉพาะออเดอร์นี้
        $result = $dbc->Update("bs_orders_bwd", $update_data, "id=" . $order_id);
        if ($result) {
            $updated_orders[] = $order_id;
        }

        // หาและอัปเดต delivery record จากออเดอร์หลัก
        $parent_order = $dbc->GetRecord("bs_orders_bwd", "delivery_id", "id=" . $order['parent']);
        if ($parent_order && $parent_order['delivery_id']) {
            $dbc->Update("bs_deliveries_bwd", $update_data, "id=" . $parent_order['delivery_id']);
        }
    }

    if (count($updated_orders) > 0) {
        // บันทึก log
        $updated_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "order-bwd-postpone", $order_id, array("bs_orders-bwd" => $updated_order));

        echo json_encode(array(
            'success' => true,
            'msg' => 'อัปเดตวันที่ส่งของสำเร็จ (' . count($updated_orders) . ' รายการ)',
            'updated_orders' => $updated_orders
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Postpone order error: ' . $e->getMessage());
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
