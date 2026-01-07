<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

header('Content-Type: application/json');

$dbc = new datastore;
$dbc->Connect();

$split_id = isset($_POST['split_id']) ? intval($_POST['split_id']) : 0;

// Validate input
if ($split_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    $dbc->Close();
    exit;
}

$check_sql = "
SELECT parent_order_id, split_amount, split_total 
FROM bs_orders_split_bwd 
WHERE id = $split_id AND status = 1
";
$rst = $dbc->Query($check_sql);
$split_data = $dbc->Fetch($rst);

if (!$split_data) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล Split Order']);
    $dbc->Close();
    exit;
}

$parent_order_id = intval($split_data[0]);

$count_sql = "
SELECT COUNT(*) as split_count,
       SUM(split_amount) as total_split_amount,
       SUM(split_total) as total_split_total
FROM bs_orders_split_bwd 
WHERE parent_order_id = $parent_order_id AND status = 1
";
$rst = $dbc->Query($count_sql);
$count_data = $dbc->Fetch($rst);

$split_count = intval($count_data[0]);
$total_split_amount = floatval($count_data[1]);
$total_split_total = floatval($count_data[2]);

$dbc->Query("START TRANSACTION");

try {
    $delete_sql = "
    UPDATE bs_orders_split_bwd 
    SET status = 0 
    WHERE parent_order_id = $parent_order_id AND status = 1
    ";

    if ($dbc->Query($delete_sql)) {
        $dbc->Query("COMMIT");

        echo json_encode([
            'success' => true,
            'message' => 'Unsplit สำเร็จ (' . $split_count . ' รายการ)',
            'data' => array(
                'parent_order_id' => $parent_order_id,
                'split_count' => $split_count,
                'total_split_amount' => number_format($total_split_amount, 4),
                'total_split_total' => number_format($total_split_total, 2)
            )
        ]);
    } else {
        throw new Exception('เกิดข้อผิดพลาดในการ Unsplit');
    }
} catch (Exception $e) {
    $dbc->Query("ROLLBACK");

    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

$dbc->Close();
