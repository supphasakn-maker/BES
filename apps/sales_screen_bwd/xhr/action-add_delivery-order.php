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
    $os = new oceanos($dbc);

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    if (!isset($_POST['id']) || !isset($_POST['delivery_date']) || empty($_POST['delivery_date'])) {
        throw new Exception('Order ID และ Delivery Date เป็นค่าที่จำเป็น');
    }

    $order_id      = intval($_POST['id']);
    $delivery_date = $_POST['delivery_date'];

    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$order) {
        throw new Exception('ไม่พบรายการ Order ที่ระบุ');
    }

    if (!is_null($order['parent'])) {
        throw new Exception('กรุณาเลือก Order หลัก (parent) เพื่อแก้ไขวันที่จัดส่ง');
    }

    if (empty($order['delivery_id']) || intval($order['delivery_id']) <= 0) {
        throw new Exception('ไม่พบ delivery_id ในออเดอร์นี้ จึงไม่สามารถอัพเดตได้ (ระบบไม่สร้างใหม่)');
    }

    $delivery_id = intval($order['delivery_id']);

    $delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $delivery_id);
    if (!$delivery) {
        throw new Exception('ไม่พบข้อมูล Delivery (id=' . $delivery_id . ') ของออเดอร์นี้');
    }

    $sum_sql = "
        SELECT SUM(amount) AS total_amount
        FROM bs_orders_bwd
        WHERE status > 0
          AND delivery_id = {$delivery_id}
    ";
    $sum_rst = $dbc->Query($sum_sql);
    $total_amount = 0.0;

    if ($sum_row = $dbc->Fetch($sum_rst)) {
        $total_amount = (float)$sum_row['total_amount'];
    }

    if ($total_amount <= 0 && isset($delivery['amount'])) {
        $total_amount = (float)$delivery['amount'];
    }

    $dbc->Update(
        "bs_deliveries_bwd",
        array(
            "delivery_date" => $delivery_date,
            "#amount"       => $total_amount,  
            "#updated"      => "NOW()"
        ),
        "id=" . $delivery_id
    );

    $count_sql = "
        SELECT COUNT(*) AS cnt
        FROM bs_orders_bwd
        WHERE delivery_id = {$delivery_id}
    ";
    $count_rst = $dbc->Query($count_sql);
    $updated_orders_count = 0;
    if ($count_row = $dbc->Fetch($count_rst)) {
        $updated_orders_count = intval($count_row['cnt']);
    }

    $dbc->Update(
        "bs_orders_bwd",
        array(
            "delivery_date" => $delivery_date
        ),
        "delivery_id = " . $delivery_id
    );

    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "update-delivery-date-and-amount-by-delivery-id",
        $delivery_id,
        array(
            "order_id"          => $order_id,
            "delivery_id"       => $delivery_id,
            "code"              => $delivery['code'] ?? null,
            "new_delivery_date" => $delivery_date,
            "new_amount"        => $total_amount,
            "updated_orders"    => $updated_orders_count
        )
    );

    echo json_encode(array(
        'success'      => true,
        'msg'          => 'อัพเดตวันที่จัดส่งและ amount จาก delivery_id เรียบร้อยแล้ว (รหัส: ' . ($delivery['code'] ?? '') . ', ' . $updated_orders_count . ' รายการ)',
        'code'         => $delivery['code'] ?? null,
        'delivery_id'  => $delivery_id,
        'amount'       => $total_amount,
        'is_update'    => true
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg'     => 'Error: ' . $e->getMessage()
    ));
    error_log('Update delivery by delivery_id error: ' . $e->getMessage());
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
