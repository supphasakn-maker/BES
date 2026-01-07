<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', 0); // ปิด error display
error_reporting(0); // ปิด error reporting
date_default_timezone_set(DEFAULT_TIMEZONE);

// Clean output buffer ก่อน
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

$dbc = new datastore;
$dbc->Connect();
$os = new oceanos($dbc);

// Include OrderSplitManager
include_once "../include/order_split_manager.php";

header('Content-Type: application/json');

try {
    // Debug: Log incoming data
    error_log("=== Unsplit Order Debug ===");
    error_log("POST data: " . print_r($_POST, true));

    if (empty($_SESSION['auth']['user_id'])) {
        throw new Exception('คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
    }

    if (empty($_POST['parent'])) {
        throw new Exception('ไม่พบ Parent Order ID');
    }

    $parent = intval($_POST['parent']);
    error_log("Parent ID: " . $parent);

    // หา split records ทั้งหมดที่มี parent นี้ ด้วย SQL โดยตรง
    $split_sql = "SELECT * FROM bs_orders_profit WHERE parent = " . $parent . " AND is_split = 1 ORDER BY split_sequence ASC";
    $split_result = $dbc->Query($split_sql);

    $split_records = array();
    while ($row = $dbc->Fetch($split_result)) {
        $split_records[] = array(
            'id' => $row[0],
            'order_id' => $row[39],
            'split_sequence' => $row[10],
            'amount' => $row[13]
        );
    }

    error_log("Found split records: " . count($split_records));

    if (empty($split_records)) {
        throw new Exception('ไม่พบ Split Records ที่เกี่ยวข้อง');
    }

    // หา original record (split_sequence = 1)
    $original_record = null;
    foreach ($split_records as $record) {
        if ($record['split_sequence'] == 1) {
            $original_record = $record;
            break;
        }
    }

    if (!$original_record) {
        throw new Exception('ไม่พบ Original Record');
    }

    error_log("Original record found: order_id = " . $original_record['order_id']);

    // เก็บข้อมูลก่อน unsplit สำหรับ log
    $before_unsplit = array(
        'split_count' => count($split_records),
        'parent_id' => $parent,
        'original_order_id' => $original_record['order_id']
    );

    // ทำการ unsplit
    $splitManager = new OrderSplitManager($dbc);
    $result = $splitManager->unsplitOrder($original_record['order_id']);

    error_log("Unsplit result: " . print_r($result, true));

    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    // บันทึก log
    if (method_exists($os, 'save_log')) {
        try {
            $os->save_log(0, $_SESSION['auth']['user_id'], "order-unsplit", $original_record['order_id'], $before_unsplit);
        } catch (Exception $e) {
            error_log("Log error: " . $e->getMessage());
            // ไม่ throw error เพื่อไม่ให้กระทบกับ unsplit ที่สำเร็จแล้ว
        }
    }

    $response = array(
        'success' => true,
        'msg' => 'Unsplit Order สำเร็จ รวม ' . count($split_records) . ' รายการกลับเป็น 1 รายการ',
        'data' => array(
            'parent' => $parent,
            'original_order_id' => $original_record['order_id'],
            'merged_count' => count($split_records)
        )
    );

    error_log("Final response: " . print_r($response, true));
} catch (Exception $e) {
    error_log("Exception in unsplit: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    $response = array(
        'success' => false,
        'msg' => $e->getMessage()
    );
} catch (Error $e) {
    error_log("Error in unsplit: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    $response = array(
        'success' => false,
        'msg' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()
    );
}

// Clean output และส่ง JSON
$output = ob_get_clean();
if (!empty($output)) {
    error_log("Unexpected output: " . $output);
}

echo json_encode($response);

$dbc->Close();
