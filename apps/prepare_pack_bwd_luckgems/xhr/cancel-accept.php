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

    $VERSION = 'cancel_accept_v1';

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้ว โปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    if (empty($_POST['id'])) {
        throw new Exception('Order ID เป็นค่าที่จำเป็น');
    }

    $order_id = intval($_POST['id']);

    $parent_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$parent_order) {
        throw new Exception('ไม่พบรายการ Order ที่ระบุ');
    }
    if (!is_null($parent_order['parent'])) {
        throw new Exception('กรุณาเลือก Order หลัก (parent) เพื่อยกเลิกการรับ');
    }

    $dbc->Query("START TRANSACTION");

    $updated_orders_count = 0;

    if ($dbc->Update("bs_orders_bwd", array(
        "#accept_date" => "NULL",
        "#accept" => 0
    ), "id=" . $order_id)) {
        $updated_orders_count++;
    }

    $child_rst = $dbc->Query("SELECT id FROM bs_orders_bwd WHERE parent=" . $order_id);
    while ($child = $dbc->Fetch($child_rst)) {
        $cid = intval($child['id']);
        if ($dbc->Update("bs_orders_bwd", array(
            "#accept_date" => "NULL",
            "#accept" => 0
        ), "id=" . $cid)) {
            $updated_orders_count++;
        }
    }

    $dbc->Query("COMMIT");

    // Log
    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "cancel-accept",
        $order_id,
        array(
            "version" => $VERSION,
            "parent_order_id" => $order_id,
            "updated_orders_count" => $updated_orders_count
        )
    );

    echo json_encode(array(
        'success' => true,
        'version' => $VERSION,
        'file' => __FILE__,
        'msg' => 'ยกเลิกการรับเรียบร้อยแล้ว (ออเดอร์ที่อัปเดต: ' . $updated_orders_count . ' รายการ)',
        'parent_order_id' => $order_id,
        'updated_orders' => $updated_orders_count
    ));
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode(array(
        'success' => false,
        'version' => 'cancel_accept_v1',
        'file' => __FILE__,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Cancel accept error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
