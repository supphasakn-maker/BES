<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();
$os = new oceanos($dbc);

include_once "../include/order_split_manager.php";

header('Content-Type: application/json');

error_log("=== Split Order Debug ===");
error_log("POST data: " . print_r($_POST, true));
error_log("Session: " . print_r($_SESSION, true));

try {
    if (empty($_SESSION['auth']['user_id'])) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
        ));
        exit();
    }

    if (empty($_POST['order_id'])) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'ไม่พบ Order ID'
        ));
        exit();
    }

    if (empty($_POST['split_amount']) || !is_array($_POST['split_amount'])) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'กรุณาระบุจำนวนที่ต้องการแบ่ง'
        ));
        exit();
    }

    $order_id = intval($_POST['order_id']);
    $split_amounts = $_POST['split_amount'];

    error_log("Order ID: " . $order_id);
    error_log("Split amounts: " . print_r($split_amounts, true));

    $clean_amounts = array();
    foreach ($split_amounts as $amount) {
        $amount = floatval($amount);
        if ($amount > 0) {
            $clean_amounts[] = $amount;
        }
    }

    error_log("Clean amounts: " . print_r($clean_amounts, true));

    if (count($clean_amounts) < 2) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'ต้องมีการแบ่งอย่างน้อย 2 รายการ'
        ));
        exit();
    }

    $check_sql = "SELECT * FROM bs_orders_profit WHERE order_id = " . intval($order_id);
    $check_result = $dbc->Query($check_sql);
    $order_row = $dbc->Fetch($check_result);


    if (!$order_row) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'ไม่พบข้อมูล Order'
        ));
        exit();
    }

    $order = array(
        'order_id' => $order_row[39],
        'amount' => $order_row[13],
        'is_split' => $order_row[9],
        'price' => $order_row[14],
        'code' => $order_row[1],
        'customer_name' => $order_row[3]
    );

    error_log("Order details - ID: " . $order['order_id'] . ", Amount: " . $order['amount'] . ", Is Split: " . $order['is_split']);

    if ($order['is_split'] == 1) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Order นี้ถูก split แล้ว'
        ));
        exit();
    }

    $total_split = array_sum($clean_amounts);
    if (abs($total_split - $order['amount']) > 0.0001) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'ยอดรวมของการแบ่ง (' . number_format($total_split, 4) . ') ไม่ตรงกับยอดเดิม (' . number_format($order['amount'], 4) . ')'
        ));
        exit();
    }

    $splitManager = new OrderSplitManager($dbc);
    $result = $splitManager->splitOrder($order_id, $clean_amounts);


    if (!$result['success']) {
        echo json_encode(array(
            'success' => false,
            'msg' => $result['message']
        ));
        exit();
    }

    if (method_exists($os, 'save_log')) {
        $os->save_log(0, $_SESSION['auth']['user_id'], "order-split", $order_id, array(
            "order" => $order,
            "split_amounts" => $clean_amounts,
            "split_count" => count($clean_amounts)
        ));
    }

    echo json_encode(array(
        'success' => true,
        'msg' => 'Split Order สำเร็จ แบ่งเป็น ' . count($clean_amounts) . ' รายการ',
        'data' => array(
            'order_id' => $order_id,
            'split_count' => count($clean_amounts),
            'amounts' => $clean_amounts
        )
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ));
} catch (Error $e) {

    echo json_encode(array(
        'success' => false,
        'msg' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()
    ));
}

$dbc->Close();
