<?php
session_start();
@ini_set('display_errors', 0);
header('Content-Type: application/json; charset=UTF-8');

require_once "../../../config/define.php";
require_once "../../../include/db.php";
require_once "../../../include/oceanos.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

// ตรวจ session
if (empty($_SESSION['auth']['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่']);
    exit;
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$tracking = isset($_POST['tracking']) ? trim($_POST['tracking']) : '';

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'msg' => 'order_id ไม่ถูกต้อง']);
    exit;
}

// อนุญาตเฉพาะ A-Z a-z 0-9 - _
$tracking = preg_replace('/[^A-Za-z0-9\-_]/', '', $tracking);

// ระวัง: `Tracking` เป็นตัวพิมพ์ใหญ่ ต้องใส่ backtick
$escaped = $dbc->Escape_String($tracking);
$sql = "UPDATE bs_orders 
        SET `Tracking` = " . ($tracking === '' ? "NULL" : "'{$escaped}'") . "
        WHERE id = {$order_id}
        LIMIT 1";

$ok = $dbc->Query($sql);

echo json_encode(['success' => (bool)$ok, 'msg' => $ok ? null : 'อัปเดตไม่สำเร็จ']);
$dbc->Close();
