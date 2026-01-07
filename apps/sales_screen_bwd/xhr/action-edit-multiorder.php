<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);
header('Content-Type: application/json');

$VERSION = 'edit_multiorder_v12_BWD2_PERBOX_SHIPPING_DETAILED_WITH_DELIVERY_UPDATE_ORDERPLATFORM';

try {
    $dbc = new dbc;
    $dbc->Connect();
    $os = new oceanos($dbc);

    function sqlv($s)
    {
        if ($s === null) return "NULL";
        return "'" . addslashes((string)$s) . "'";
    }

    function setDateTimeRaw(&$arr, $field, $value)
    {
        $val = isset($value) ? trim((string)$value) : '';
        $arr["#{$field}"] =
            "COALESCE(" .
            "STR_TO_DATE(NULLIF(" . sqlv($val) . ", ''), '%Y-%m-%d %H:%i:%s')," .
            "STR_TO_DATE(NULLIF(" . sqlv($val) . ", ''), '%Y-%m-%d')" .
            ")";
    }

    function toIntOrZero($v)
    {
        return is_numeric($v) ? (int)$v : 0;
    }

    function toDec4($v)
    {
        $f = 0.0;
        if ($v !== '' && $v !== null && is_numeric($v)) $f = (float)$v;
        if ($f < 0) $f = 0.0;
        return (float)number_format($f, 4, '.', '');
    }

    function toDec2($v)
    {
        $f = 0.0;
        if ($v !== '' && $v !== null && is_numeric($v)) $f = (float)$v;
        if ($f < 0) $f = 0.0;
        return (float)number_format($f, 2, '.', '');
    }

    function sanitize_orderable_type($v)
    {
        $allow = ['delivered_by_company', 'post_office', 'receive_at_company', 'receive_at_luckgems'];
        $v = trim((string)$v);
        return in_array($v, $allow, true) ? $v : '';
    }

    function is_discount_platform($platform)
    {
        $p = strtolower(trim((string)$platform));
        return in_array($p, ['shopee', 'lazada', 'tiktok'], true);
    }

    function calculateShippingPerBox($boxItems, $boxTotal, $isRemote, $orderableType, $shippingMethod)
    {
        if ($shippingMethod === 4 || $shippingMethod === '4') {
            return [
                'base' => 0,
                'box_fee' => 0,
                'remote_fee' => 0,
                'total' => 0,
                'wooden_count' => 0,
                'premium_count' => 0
            ];
        }

        if (empty($orderableType) || $orderableType !== 'post_office') {
            return [
                'base' => 0,
                'box_fee' => 0,
                'remote_fee' => 0,
                'total' => 0,
                'wooden_count' => 0,
                'premium_count' => 0
            ];
        }

        $baseShipping = 0;
        if ($boxTotal > 0 && $boxTotal <= 14999) {
            $baseShipping = 50;
        } elseif ($boxTotal >= 15000 && $boxTotal <= 50000) {
            $baseShipping = 100;
        } elseif ($boxTotal > 50000) {
            $baseShipping = 100;
        }

        $woodenBoxCount = 0;
        $premiumBoxCount = 0;

        $woodenBoxTypes = [17, 18, 19, 20];
        $premiumBoxTypes = [13, 14, 15, 16, 21, 22, 23, 24, 25];

        foreach ($boxItems as $item) {
            $productTypeId = (int)$item['product_type'];
            $amount = (float)$item['amount'];

            if (in_array($productTypeId, $woodenBoxTypes)) {
                $woodenBoxCount += $amount;
            }

            if (in_array($productTypeId, $premiumBoxTypes)) {
                $premiumBoxCount += $amount;
            }
        }

        $boxFee = ($woodenBoxCount * 100) + ($premiumBoxCount * 25);

        $remoteFee = 0;
        if ($isRemote) {
            $remoteFee = 50;
        }

        return [
            'base' => $baseShipping,
            'box_fee' => $boxFee,
            'remote_fee' => $remoteFee,
            'total' => $baseShipping + $boxFee + $remoteFee,
            'wooden_count' => $woodenBoxCount,
            'premium_count' => $premiumBoxCount
        ];
    }


    if (empty($_POST['main_order_id'])) {
        echo json_encode(['success' => false, 'msg' => "ไม่พบ ID ของออเดอร์หลัก"]);
        exit;
    }
    $main_order_id = (int)$_POST['main_order_id'];

    $main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order_id);
    if (!$main_order) {
        echo json_encode(['success' => false, 'msg' => "ไม่พบออเดอร์หลักที่ต้องการแก้ไข"]);
        exit;
    }

    if (empty($_POST['customer_name']) || empty($_POST['platform'])) {
        echo json_encode(['success' => false, 'msg' => "กรุณากรอกข้อมูลลูกค้าให้ครบถ้วน"]);
        exit;
    }
    if (!isset($_POST['orders']) || !is_array($_POST['orders'])) {
        echo json_encode(['success' => false, 'msg' => "ไม่พบข้อมูลรายการสินค้า"]);
        exit;
    }

    $current_platform = $_POST['platform'];
    $order_platform   = isset($_POST['order_platform']) ? trim((string)$_POST['order_platform']) : '';

    $platform_lc = strtolower(trim((string)$current_platform));
    $marketplace_platforms = ['shopee', 'lazada', 'tiktok', 'silvernow'];

    if (in_array($platform_lc, $marketplace_platforms, true) && $order_platform === '') {
        echo json_encode([
            'success' => false,
            'msg'     => 'Please input order_platform for marketplace platform',
        ]);
        exit;
    }


    $orderable_type        = sanitize_orderable_type($_POST['orderable_type'] ?? '');
    $is_remote             = isset($_POST['is_remote']) && $_POST['is_remote'] == '1' ? true : false;
    $fee                   = toDec4($_POST['fee'] ?? 0);
    $shipping_method = isset($_POST['shipping']) ? $_POST['shipping'] : '';
    if ($shipping_method !== '' && $shipping_method !== null) {
        $shipping_method = (int)$shipping_method;
    } else {
        $shipping_method = 0;
    }

    $is_discount_platform  = is_discount_platform($current_platform);

    $delivery_date_input   = $_POST['delivery_date'] ?? '';
    $order_date_input      = $_POST['date'] ?? '';

    $pending_updates             = [];
    $box_totals_no_shipping      = [];
    $box_items_grouped           = [];
    $total_net                   = 0.0;
    $main_order_net_before_fee   = null;


    foreach ($_POST['orders'] as $index => $order_data) {

        $order_id   = (int)($order_data['id'] ?? 0);
        $is_main    = ((int)($order_data['is_main'] ?? 0) === 1);

        if ($order_id <= 0) {
            throw new Exception("ไม่พบ ID รายการที่ " . ($index + 1));
        }

        $existing_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
        if (!$existing_order) {
            throw new Exception("ไม่พบออเดอร์ ID: " . $order_id);
        }

        $amount       = (float)($order_data['amount'] ?? 0);
        $price        = (float)($order_data['price']  ?? 0);
        $product_id   = (int)  ($order_data['product_id']   ?? 0);
        $product_type = (int)  ($order_data['product_type'] ?? 0);

        if ($amount <= 0 || $price < 0 || $product_id <= 0 || $product_type <= 0) {
            throw new Exception("ข้อมูลรายการที่ " . ($index + 1) . " ไม่ครบถ้วน");
        }

        $discount_type = (int)($order_data['discount_type'] ?? 0);
        $ai            = (int)($order_data['ai'] ?? 0);
        $engrave       = $order_data['engrave'] ?? 'ไม่สลักข้อความบนแท่งเงิน';

        $total    = $amount * $price;
        $discount = $discount_type > 0 ? $total * ($discount_type / 100.0) : 0.0;

        $ai_cost      = ($ai === 1 && !$is_discount_platform) ? ($amount * 400) : 0.0;
        $engrave_cost = ($engrave === 'สลักข้อความบนแท่งเงิน' && !$is_discount_platform) ? ($amount * 300) : 0.0;

        $net_no_shipping = $total - $discount + $ai_cost + $engrave_cost;

        $box_number = isset($existing_order['box_number']) ? (int)$existing_order['box_number'] : 0;

        if (!isset($box_totals_no_shipping[$box_number])) {
            $box_totals_no_shipping[$box_number] = 0.0;
            $box_items_grouped[$box_number] = [];
        }
        $box_totals_no_shipping[$box_number] += $net_no_shipping;
        $box_items_grouped[$box_number][] = [
            'product_type' => $product_type,
            'amount' => $amount
        ];

        $pending_updates[] = [
            'order_id'          => $order_id,
            'is_main'           => $is_main,
            'amount'            => $amount,
            'price'             => $price,
            'discount_type'     => $discount_type,
            'discount'          => $discount,
            'total'             => $total,
            'product_id'        => $product_id,
            'product_type'      => $product_type,
            'engrave'           => $engrave,
            'font'              => $order_data['font'] ?? null,
            'carving'           => $order_data['carving'] ?? null,
            'ai'                => $ai,
            'net_no_shipping'   => $net_no_shipping,
            'ai_cost'           => $ai_cost,
            'engrave_cost'      => $engrave_cost,
            'box_number'        => $box_number
        ];
    }

    $needsBoxSplit = ($orderable_type === 'post_office' || $orderable_type === 'delivered_by_company');

    if ($needsBoxSplit) {
        foreach ($box_totals_no_shipping as $box_no => $sum_no_ship) {
            if ($sum_no_ship > 50000.0001) {
                $msg = sprintf(
                    "ยอดรวมของกล่องที่ %d เท่ากับ %s บาท ซึ่งเกิน 50,000 บาท/กล่อง " .
                        "กรุณาเพิ่มกล่องใหม่ หรือปรับจำนวน/ราคาต่อแท่งให้ไม่เกิน 50,000 บาทต่อกล่อง",
                    $box_no + 1,
                    number_format($sum_no_ship, 2)
                );
                throw new Exception($msg);
            }
        }
    }
    $box_shipping_costs = [];
    $total_shipping = 0.0;

    foreach ($box_items_grouped as $box_no => $items) {
        $boxTotal = $box_totals_no_shipping[$box_no];

        $shippingCalc = calculateShippingPerBox(
            $items,
            $boxTotal,
            $is_remote,
            $orderable_type,
            $shipping_method
        );

        $box_shipping_costs[$box_no] = $shippingCalc;
        $total_shipping += $shippingCalc['total'];
    }


    $dbc->Query("START TRANSACTION");

    foreach ($pending_updates as &$u) {
        $box_no = $u['box_number'];
        $shipping_calc = $box_shipping_costs[$box_no];

        $net = $u['net_no_shipping'] + $shipping_calc['total'];
        $u['net'] = $net;

        $total_net += $net;
        if ($u['is_main']) {
            $main_order_net_before_fee = $net;
        }

        $update_data = [
            '#amount'              => $u['amount'],
            '#price'               => $u['price'],
            '#discount_type'       => $u['discount_type'],
            '#discount'            => $u['discount'],
            '#total'               => $u['total'],
            '#net'                 => $net,
            '#shipping_base'       => toDec2($shipping_calc['base']),
            '#shipping_box_fee'    => toDec2($shipping_calc['box_fee']),
            '#shipping_remote_fee' => toDec2($shipping_calc['remote_fee']),
            '#shipping_total'      => toDec2($shipping_calc['total']),
            '#product_id'          => $u['product_id'],
            '#product_type'        => $u['product_type'],
            'engrave'              => $u['engrave'],
            'font'                 => $u['font'],
            'carving'              => $u['carving'],
            'ai'                   => $u['ai'],
            '#updated'             => 'NOW()',
        ];

        if ($orderable_type !== '') {
            $update_data['orderable_type'] = $orderable_type;
        } else {
            $update_data['orderable_type'] = null;
        }

        if ($u['is_main']) {
            $update_data['customer_name']    = $_POST['customer_name'];
            $update_data['phone']            = $_POST['phone'] ?? null;
            $update_data['platform']         = $_POST['platform'];
            $update_data['order_platform']   = ($order_platform !== '') ? $order_platform : null;

            if (isset($_POST['vat_type']) && $_POST['vat_type'] !== '') {
                $update_data['#vat_type'] = (int)$_POST['vat_type'];
            }

            setDateTimeRaw($update_data, 'date', $order_date_input);
            $update_data['#shipping']        = toIntOrZero($_POST['shipping'] ?? 0);
            $update_data['shipping_address'] = $_POST['shipping_address'] ?? null;
            $update_data['billing_address']  = $_POST['billing_address'] ?? null;
            $update_data['comment']          = $_POST['comment'] ?? null;
            $update_data['Tracking']         = $_POST['Tracking'] ?? null;
        } else {
            $update_data['platform']       = $_POST['platform'];
            $update_data['order_platform'] = ($order_platform !== '') ? $order_platform : null;
        }

        unset($update_data['date']);

        if (!$dbc->Update("bs_orders_bwd", $update_data, "id=" . $u['order_id'])) {
            throw new Exception("ไม่สามารถอัปเดตรายการ ID " . $u['order_id'] . " ได้");
        }
    }

    if ($main_order_net_before_fee === null) {
        throw new Exception("ไม่พบรายการหลักสำหรับการหักค่าธรรมเนียม");
    }

    $main_order_net_after_fee = (float)number_format(($main_order_net_before_fee - $fee), 4, '.', '');
    if ($main_order_net_after_fee < 0) $main_order_net_after_fee = 0.0000;

    $dbc->Update(
        "bs_orders_bwd",
        ['#fee' => $fee, '#net' => $main_order_net_after_fee],
        "id=" . $main_order_id
    );

    $grand_total_after_fee = (float)number_format(($total_net - $fee), 4, '.', '');
    if ($grand_total_after_fee < 0) $grand_total_after_fee = 0.0000;


    $delivery_ids = [];
    $rst_dids = $dbc->Query("
        SELECT DISTINCT delivery_id
        FROM bs_orders_bwd
        WHERE (id = {$main_order_id} OR parent = {$main_order_id})
          AND delivery_id IS NOT NULL
    ");
    while ($row = $dbc->Fetch($rst_dids)) {
        $delivery_ids[] = (int)$row['delivery_id'];
    }
    $delivery_ids = array_values(array_unique(array_filter($delivery_ids, fn($v) => $v > 0)));

    foreach ($delivery_ids as $del_id) {
        $sum_result = $dbc->GetRecord(
            "bs_orders_bwd",
            "SUM(amount) as total_amount",
            "delivery_id = " . $del_id
        );
        $total_amount = $sum_result ? (float)$sum_result['total_amount'] : 0;

        $delivery_order = $dbc->GetRecord("bs_orders_bwd", "code", "id=" . $main_order_id);
        $order_code_for_comment = $delivery_order['code'] ?? '';

        $delivery_comment = "Order: " . $order_code_for_comment . " | ";
        $delivery_comment .= "จำนวนกล่อง: " . count($box_shipping_costs) . " | ";

        foreach ($box_shipping_costs as $box_idx => $box_ship) {
            $delivery_comment .= sprintf(
                "กล่อง %d: %.2f บาท (ฐาน:%.2f+กล่อง:%.2f+ห่างไกล:%.2f) | ",
                $box_idx + 1,
                $box_ship['total'],
                $box_ship['base'],
                $box_ship['box_fee'],
                $box_ship['remote_fee']
            );
        }

        $delivery_comment .= "รวมค่าส่ง: " . number_format($total_shipping, 2) . " บาท";

        $dbc->Update(
            "bs_deliveries_bwd",
            [
                '#amount' => $total_amount,
                'comment' => $delivery_comment,
                'delivery_method' => $orderable_type,
                '#updated' => 'NOW()'
            ],
            "id=" . $del_id
        );
    }

    $dbc->Query("COMMIT");


    try {
        $user_id = $os->auth['id'] ?? 0;
        foreach ($pending_updates as $u) {
            $order_data = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $u['order_id']);
            $os->save_log(0, $user_id, "bwd2-multi-order-edit", $u['order_id'], ["bwd-orders2" => $order_data]);
        }
    } catch (Exception $log_error) {
        error_log("Log save error: " . $log_error->getMessage());
    }

    $main_order_after = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order_id);
    $platform_info = $is_discount_platform ? " (Marketplace: no AI/engrave fee)" : " (Standard Platform)";

    $shipping_breakdown = [];
    foreach ($box_shipping_costs as $box_no => $calc) {
        $shipping_breakdown[] = [
            'box_number' => $box_no + 1,
            'base' => $calc['base'],
            'box_fee' => $calc['box_fee'],
            'remote_fee' => $calc['remote_fee'],
            'total' => $calc['total'],
            'wooden_count' => $calc['wooden_count'],
            'premium_count' => $calc['premium_count']
        ];
    }

    echo json_encode([
        'success'              => true,
        'version'              => $VERSION,
        'file'                 => __FILE__,
        'msg'                  => "แก้ไขข้อมูลออเดอร์สำเร็จ (" . count($pending_updates) . " รายการ)" . $platform_info,
        'main_order_id'        => $main_order_id,
        'order_code'           => $main_order_after['code'] ?? null,
        'updated_count'        => count($pending_updates),
        'subtotal_before_fee'  => number_format($total_net, 4, '.', ''),
        'fee'                  => number_format($fee, 4, '.', ''),
        'total_after_fee'      => number_format($grand_total_after_fee, 4, '.', ''),
        'total_shipping'       => number_format($total_shipping, 4, '.', ''),
        'platform'             => $current_platform,
        'is_discount_platform' => $is_discount_platform,
        'is_remote'            => $is_remote,
        'delivery_date_input'  => $delivery_date_input,
        'order_date_input'     => $order_date_input,
        'orderable_type'       => $orderable_type,
        'affected_deliveries'  => $delivery_ids,
        'shipping_breakdown'   => $shipping_breakdown
    ]);
} catch (Exception $e) {
    if (isset($dbc)) {
        @$dbc->Query("ROLLBACK");
    }
    echo json_encode([
        'success' => false,
        'version' => $VERSION,
        'file'    => __FILE__,
        'msg'     => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ]);
    error_log('Edit multiorder error: ' . $e->getMessage() . ' @' . __FILE__);
} finally {
    if (isset($dbc)) $dbc->Close();
}
