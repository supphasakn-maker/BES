<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

header('Content-Type: application/json');

try {
    $dbc = new dbc;
    $dbc->Connect();

    $input = json_decode(file_get_contents('php://input'), true);
    $order_id = isset($input['order_id']) ? (int)$input['order_id'] : 0;

    if (!$order_id) {
        echo json_encode(['success' => false, 'msg' => 'Invalid order ID']);
        exit;
    }

    $parent = $dbc->GetRecord(
        "bs_orders_bwd",
        "*",
        "id = {$order_id} AND parent IS NULL"
    );

    if (!$parent) {
        echo json_encode(['success' => false, 'msg' => 'Order not found']);
        exit;
    }

    $sql = "
        SELECT 
            o.id,
            o.box_number,
            o.Tracking,
            o.delivery_pack,
            o.amount,
            o.price,
            o.total,
            o.product_id,
            o.product_type,
            o.engrave,
            o.ai,
            o.carving,
            o.shipping_base,
            o.shipping_box_fee,
            o.shipping_remote_fee,
            o.shipping_total,
            pt.name as product_type_name
        FROM bs_orders_bwd o
        LEFT JOIN bs_products_type pt ON o.product_type = pt.id
        WHERE (o.id = {$order_id} OR o.parent = {$order_id})
          AND o.status > 0
        ORDER BY o.box_number ASC, o.id ASC
    ";

    $result = $dbc->Query($sql);
    $boxes = [];

    while ($row = $dbc->Fetch($result)) {
        $boxNum = (int)$row['box_number'];

        if (!isset($boxes[$boxNum])) {
            $boxes[$boxNum] = [
                'id' => (int)$row['id'],
                'box_number' => $boxNum,
                'tracking' => $row['Tracking'] ?? '',
                'delivery_pack' => (int)$row['delivery_pack'],
                'items' => [],
                'item_count' => 0,
                'total_amount' => 0,
                'shipping_base' => 0,
                'shipping_box_fee' => 0,
                'shipping_remote_fee' => 0,
                'shipping_total' => 0
            ];
        }

        if ($boxes[$boxNum]['item_count'] === 0) {
            $boxes[$boxNum]['shipping_base'] = (float)($row['shipping_base'] ?? 0);
            $boxes[$boxNum]['shipping_box_fee'] = (float)($row['shipping_box_fee'] ?? 0);
            $boxes[$boxNum]['shipping_remote_fee'] = (float)($row['shipping_remote_fee'] ?? 0);
            $boxes[$boxNum]['shipping_total'] = (float)($row['shipping_total'] ?? 0);
        }

        $productDetail = [];
        $productDetail['type_name'] = $row['product_type_name'] ?? 'สินค้า';
        $productDetail['product_id'] = (int)$row['product_id'];
        $productDetail['amount'] = (float)$row['amount'];
        $productDetail['price'] = (float)$row['price'];
        $productDetail['total'] = (float)$row['total'];

        $extras = [];
        if (!empty($row['engrave']) && $row['engrave'] !== 'ไม่สลักข้อความบนแท่งเงิน') {
            $extras[] = 'สลัก: ' . ($row['carving'] ?? '');
        }
        if (!empty($row['ai']) && $row['ai'] == 1) {
            $extras[] = 'AI Design';
        }
        $productDetail['extras'] = $extras;

        $boxes[$boxNum]['items'][] = $productDetail;
        $boxes[$boxNum]['item_count']++;
        $boxes[$boxNum]['total_amount'] += $productDetail['total'];
    }

    $boxes = array_values($boxes);

    echo json_encode([
        'success' => true,
        'data' => [
            'id' => (int)$parent['id'],  
            'order_id' => (int)$parent['id'],  
            'order_code' => $parent['code'],
            'customer_name' => $parent['customer_name'],
            'phone' => $parent['phone'],
            'shipping_address' => $parent['shipping_address'],
            'boxes' => $boxes
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    if (isset($dbc)) {
        $dbc->Close();
    }
}
