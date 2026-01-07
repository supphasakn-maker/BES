<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

header('Content-Type: application/json');

$dbc = new datastore;
$dbc->Connect();

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$split_amounts = isset($_POST['split_amount']) ? $_POST['split_amount'] : array();
$user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'system';

if ($order_id <= 0 || empty($split_amounts) || !is_array($split_amounts)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    $dbc->Close();
    exit;
}

$valid_split_amounts = array();
foreach ($split_amounts as $amount) {
    $amount = floatval($amount);
    if ($amount > 0) {
        $valid_split_amounts[] = $amount;
    }
}

if (empty($valid_split_amounts)) {
    echo json_encode(['success' => false, 'message' => 'กรุณาระบุจำนวน Amount ที่ต้องการแยก']);
    $dbc->Close();
    exit;
}

$check_sql = "
SELECT 
    (SELECT COUNT(*) FROM bs_mapping_profit_orders_bwd WHERE order_id = $order_id) +
    (SELECT COUNT(*) FROM bs_mapping_profit_orders_usd_bwd WHERE order_id = $order_id) AS match_count
";
$rst = $dbc->Query($check_sql);
$check = $dbc->Fetch($rst);

if ($check[0] > 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถ Split ได้ เนื่องจาก Order นี้ถูก Match แล้ว']);
    $dbc->Close();
    exit;
}

$check_amount_sql = "
SELECT 
    SUM(CASE 
        WHEN child.product_id = 1 THEN child.amount * 0.015
        WHEN child.product_id = 2 THEN child.amount * 0.050
        WHEN child.product_id = 3 THEN child.amount * 0.150
        ELSE 0
    END) AS total_amount,
    SUM(child.total) AS total_sum,
    COALESCE((SELECT SUM(split_amount) FROM bs_orders_split_bwd WHERE parent_order_id = $order_id AND status = 1), 0) AS already_split_amount,
    COALESCE((SELECT SUM(split_total) FROM bs_orders_split_bwd WHERE parent_order_id = $order_id AND status = 1), 0) AS already_split_total
FROM bs_orders_bwd child
WHERE (child.id = $order_id OR child.parent = $order_id)
AND child.product_id IN (1,2,3)
AND child.status > 0
";

$rst = $dbc->Query($check_amount_sql);
$amount_data = $dbc->Fetch($rst);

if (!$amount_data || $amount_data[0] == 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล Order']);
    $dbc->Close();
    exit;
}

$available_amount = floatval($amount_data[0]) - floatval($amount_data[2]);
$available_total = floatval($amount_data[1]) - floatval($amount_data[3]);

$ratio = $available_amount > 0 ? ($available_total / $available_amount) : 0;

$total_split_amount = array_sum($valid_split_amounts);

if ($total_split_amount > $available_amount) {
    echo json_encode([
        'success' => false, 
        'message' => 'ยอดรวม Split Amount (' . number_format($total_split_amount, 4) . ') เกินกว่ายอดคงเหลือ (' . number_format($available_amount, 4) . ')'
    ]);
    $dbc->Close();
    exit;
}

if (abs($total_split_amount - $available_amount) > 0.0001) {
    echo json_encode([
        'success' => false, 
        'message' => 'ยอดรวม Split Amount ต้องเท่ากับยอดคงเหลือทุกบาททุกสตางค์ (เหลืออีก ' . number_format($available_amount - $total_split_amount, 4) . ')'
    ]);
    $dbc->Close();
    exit;
}

$dbc->Query("START TRANSACTION");

$success_count = 0;
$split_details = array();

try {
    foreach ($valid_split_amounts as $split_amount) {
        $split_total = round($ratio * $split_amount);
        
        $insert_sql = "
        INSERT INTO bs_orders_split_bwd (parent_order_id, split_amount, split_total, created_by, status)
        VALUES ($order_id, $split_amount, $split_total, '$user', 1)
        ";
        
        if ($dbc->Query($insert_sql)) {
            $success_count++;
            $split_details[] = array(
                'amount' => number_format($split_amount, 4),
                'total' => number_format($split_total, 2)
            );
        } else {
            throw new Exception('เกิดข้อผิดพลาดในการบันทึก Split Record');
        }
    }
    
    $dbc->Query("COMMIT");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Split Order สำเร็จ (' . $success_count . ' รายการ)',
        'data' => array(
            'count' => $success_count,
            'total_split_amount' => number_format($total_split_amount, 4),
            'ratio' => number_format($ratio, 2),
            'details' => $split_details
        )
    ]);
    
} catch (Exception $e) {
    $dbc->Query("ROLLBACK");
    
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

$dbc->Close();