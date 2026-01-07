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

if (empty($_SESSION['auth']['user_id'])) {
    echo json_encode(['success' => false, 'msg' => 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่']);
    exit;
}

$order_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$tracking = isset($_POST['tracking']) ? trim($_POST['tracking']) : '';

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'msg' => 'ID ไม่ถูกต้อง']);
    exit;
}

$tracking = preg_replace('/[^A-Za-z0-9\-_,.\s()\/]/', '', $tracking);
$tracking = trim($tracking);

$tracking = preg_replace('/\s*,\s*/', ',', $tracking);
$tracking = preg_replace('/,+/', ',', $tracking);
$tracking = trim($tracking, ',');

$escaped = $dbc->Escape_String($tracking);
$sql = "UPDATE bs_orders 
        SET `Tracking` = " . ($tracking === '' ? "NULL" : "'{$escaped}'") . "
        WHERE id = {$order_id}
        LIMIT 1";

$ok = $dbc->Query($sql);

if ($ok) {
    echo json_encode([
        'success' => true,
        'msg' => 'บันทึกสำเร็จ',
        'tracking' => $tracking
    ]);
} else {
    echo json_encode([
        'success' => false,
        'msg' => 'อัปเดตไม่สำเร็จ กรุณาลองใหม่อีกครั้ง'
    ]);
}

$dbc->Close();
