<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', 0);
error_reporting(0);
date_default_timezone_set(DEFAULT_TIMEZONE);

if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

$dbc = new datastore;
$dbc->Connect();
$os = new oceanos($dbc);

include_once "../include/order_split_manager.php";

header('Content-Type: application/json');

try {
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

    // ทำการ unsplit โดยส่ง parent ไปตรงๆ
    $splitManager = new OrderSplitManager($dbc);
    $result = $splitManager->unsplitOrder($parent);

    error_log("Unsplit result: " . print_r($result, true));

    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    // บันทึก log
    if (method_exists($os, 'save_log')) {
        try {
            $os->save_log(0, $_SESSION['auth']['user_id'], "order-unsplit", $parent, $result);
        } catch (Exception $e) {
            error_log("Log error: " . $e->getMessage());
        }
    }

    $response = array(
        'success' => true,
        'msg' => $result['message'],
        'data' => array(
            'parent' => $parent
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

$output = ob_get_clean();
if (!empty($output)) {
    error_log("Unexpected output: " . $output);
}

echo json_encode($response);

$dbc->Close();
