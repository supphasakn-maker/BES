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

    $VERSION = 'set_accept_date_v1';

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้ว โปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    if (empty($_POST['id']) || empty($_POST['accept_date'])) {
        throw new Exception('Order ID และ Accept Date เป็นค่าที่จำเป็น');
    }

    $order_id = intval($_POST['id']);
    $accept_date = $_POST['accept_date'];

    $parent_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
    if (!$parent_order) {
        throw new Exception('ไม่พบรายการ Order ที่ระบุ');
    }
    if (!is_null($parent_order['parent'])) {
        throw new Exception('กรุณาเลือก Order หลัก (parent) เพื่อบันทึกวันที่รับ');
    }

    if ($parent_order['delivery_pack'] != 1) {
        throw new Exception('ต้องจัดเตรียมแพ็คก่อนถึงจะบันทึกวันที่รับได้');
    }

    $dbc->Query("START TRANSACTION");

    $updated_orders_count = 0;

    if ($dbc->Update("bs_orders_bwd", array(
        "accept_date" => $accept_date,
        "#accept" => 1
    ), "id=" . $order_id)) {
        $updated_orders_count++;
    }

    $child_rst = $dbc->Query("SELECT id FROM bs_orders_bwd WHERE parent=" . $order_id);
    while ($child = $dbc->Fetch($child_rst)) {
        $cid = intval($child['id']);
        if ($dbc->Update("bs_orders_bwd", array(
            "accept_date" => $accept_date,
            "#accept" => 1
        ), "id=" . $cid)) {
            $updated_orders_count++;
        }
    }

    $dbc->Query("COMMIT");

    $os->save_log(
        0,
        $_SESSION['auth']['user_id'],
        "set-accept-date",
        $order_id,
        array(
            "version" => $VERSION,
            "parent_order_id" => $order_id,
            "accept_date" => $accept_date,
            "updated_orders_count" => $updated_orders_count
        )
    );

    echo json_encode(array(
        'success' => true,
        'version' => $VERSION,
        'file' => __FILE__,
        'msg' => 'บันทึกวันที่รับเรียบร้อยแล้ว (ออเดอร์ที่อัปเดต: ' . $updated_orders_count . ' รายการ)',
        'parent_order_id' => $order_id,
        'accept_date' => $accept_date,
        'updated_orders' => $updated_orders_count
    ));
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode(array(
        'success' => false,
        'version' => 'set_accept_date_v1',
        'file' => __FILE__,
        'msg' => 'Error: ' . $e->getMessage()
    ));
    error_log('Set accept date error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
