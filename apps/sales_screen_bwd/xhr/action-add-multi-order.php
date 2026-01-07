<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);
header('Content-Type: application/json');

$VERSION = 'add_multiorder_v5_AI_FIX';

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

function sqlv($s)
{
    if ($s === null) return "NULL";
    return "'" . addslashes((string)$s) . "'";
}

function setDateRaw(&$arr, $field, $value)
{
    $val = isset($value) ? trim((string)$value) : '';
    $arr["#{$field}"] = "DATE(NULLIF(" . sqlv($val) . ", ''))";
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

function isDiscountPlatform($platform)
{
    return in_array($platform, ['Shopee', 'Lazada', 'TikTok'], true);
}

function isMarketplacePlatform($platform)
{
    return in_array($platform, ['Shopee', 'Lazada', 'TikTok', 'SilverNow'], true);
}

function toDec4($v)
{
    $f = 0.0;
    if ($v !== '' && $v !== null && is_numeric($v)) $f = (float)$v;
    if ($f < 0) $f = 0.0;
    return (float)number_format($f, 4, '.', '');
}

function extractPostalCode($address)
{
    if (empty($address)) return null;
    if (preg_match_all('/\b(\d{5})\b/', $address, $matches)) {
        return end($matches[0]);
    }
    return null;
}

function isRemoteArea($postalCode)
{
    if (empty($postalCode)) return false;
    $code = (int)$postalCode;

    $remoteCodes = [
        20120,
        23170,
        57170,
        57180,
        57260,
        58000,
        58110,
        58120,
        58130,
        58140,
        58150,
        63150,
        63170,
        71180,
        71240,
        81150,
        81210,
        82160,
        83000,
        83001,
        83002,
        83100,
        83110,
        83111,
        83120,
        83130,
        83150,
        83151,
        84140,
        84280,
        84310,
        84320,
        84330,
        84360,
        94000,
        94001,
        94110,
        94120,
        94130,
        94140,
        94150,
        94160,
        94170,
        94180,
        94190,
        94220,
        94230,
        95000,
        95001,
        95110,
        95120,
        95130,
        95140,
        95150,
        95160,
        95170,
        96000,
        96110,
        96120,
        96130,
        96140,
        96150,
        96160,
        96170,
        96180,
        96190,
        96210,
        96220
    ];

    return in_array($code, $remoteCodes);
}

function calculateShippingPerBox($boxItems, $boxTotal, $boxNumber, $isRemote, $orderableType)
{
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

    foreach ($boxItems as $item) {
        $productTypeId = (int)$item['product_type'];
        $amount = (float)$item['amount'];

        if (in_array($productTypeId, [17, 18, 19, 20])) {
            $woodenBoxCount += $amount;
        }

        if (in_array($productTypeId, [13, 14, 15, 16, 21, 22, 23, 24, 25])) {
            $premiumBoxCount += $amount;
        }
    }

    $boxFee = 0;
    $boxFee += $woodenBoxCount * 100;
    $boxFee += $premiumBoxCount * 25;

    $remoteFee = 0;
    if ($isRemote && $orderableType === 'post_office') {
        $remoteFee = 50;
        error_log("  ✅ Remote Fee Applied: 50");
    } else {
        error_log("  ❌ No Remote Fee");
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

if (!isset($_POST['data'])) {
    echo json_encode(['success' => false, 'msg' => 'No data received']);
    exit;
}

$data = json_decode($_POST['data'], true);
if (!$data) {
    echo json_encode(['success' => false, 'msg' => 'Invalid JSON data']);
    exit;
}


if (empty($data['customer_name'])) {
    echo json_encode(['success' => false, 'msg' => 'Please input customer']);
    exit;
}
if (empty($data['platform'])) {
    echo json_encode(['success' => false, 'msg' => 'Please Select Platform']);
    exit;
}

if (isMarketplacePlatform($data['platform'])) {
    $order_platform_val = isset($data['order_platform']) ? trim($data['order_platform']) : '';
    if ($order_platform_val === '') {
        echo json_encode([
            'success' => false,
            'msg' => 'Please input order_platform for marketplace platform'
        ]);
        exit;
    }
}

if (!isset($data['vat_type']) || trim($data['vat_type']) === '') {
    echo json_encode(['success' => false, 'msg' => 'Please Select Vats']);
    exit;
}
if (empty($data['phone']) && empty($data['username'])) {
    echo json_encode(['success' => false, 'msg' => 'Please input Phone or Username!!']);
    exit;
}
if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false, 'msg' => 'Please add at least one item']);
    exit;
}

foreach ($data['items'] as $idx => $item) {
    if (empty($item['product_id'])) {
        echo json_encode(['success' => false, 'msg' => "Item #" . ($idx + 1) . " missing product_id"]);
        exit;
    }
    if (empty($item['product_type'])) {
        echo json_encode(['success' => false, 'msg' => "Item #" . ($idx + 1) . " missing product_type"]);
        exit;
    }
    if (!isset($item['amount']) || !is_numeric($item['amount']) || (float)$item['amount'] <= 0) {
        echo json_encode(['success' => false, 'msg' => "Item #" . ($idx + 1) . " invalid amount"]);
        exit;
    }
    if (!isset($item['price']) || !is_numeric($item['price']) || (float)$item['price'] < 0) {
        echo json_encode(['success' => false, 'msg' => "Item #" . ($idx + 1) . " invalid price"]);
        exit;
    }
}

try {

    $customer_id = null;
    $customer_name = trim($data['customer_name']);
    $phone = isset($data['phone']) ? trim($data['phone']) : null;
    $username = isset($data['username']) ? trim($data['username']) : null;
    $shipping_address = isset($data['shipping_address']) ? trim($data['shipping_address']) : '';
    $billing_address = isset($data['billing_address']) ? trim($data['billing_address']) : '';

    $order_platform = isset($data['order_platform']) ? trim($data['order_platform']) : '';

    $existing = null;
    if (!empty($phone)) {
        $existing = $dbc->GetRecord(
            "bs_customers_bwd",
            "*",
            "phone=" . sqlv($phone) . " LIMIT 1"
        );
    }
    if (!$existing && !empty($username)) {
        $existing = $dbc->GetRecord(
            "bs_customers_bwd",
            "*",
            "username=" . sqlv($username) . " LIMIT 1"
        );
    }

    if ($existing) {
        $customer_id = (int)$existing['id'];

        $update_data = [
            'customer_name' => $customer_name
        ];
        if (!empty($phone)) $update_data['phone'] = $phone;
        if (!empty($username)) $update_data['username'] = $username;
        if (!empty($shipping_address)) $update_data['shipping_address'] = $shipping_address;
        if (!empty($billing_address)) $update_data['billing_address'] = $billing_address;
        $update_data['#updated'] = 'NOW()';

        $dbc->Update("bs_customers_bwd", $update_data, "id=" . $customer_id);
    } else {
        $customer_data = [
            '#id' => 'DEFAULT',
            'customer_name' => $customer_name,
            'phone' => $phone,
            'username' => $username,
            'shipping_address' => $shipping_address,
            'billing_address' => $billing_address,
            '#created' => 'NOW()',
            '#updated' => 'NOW()'
        ];

        if (!$dbc->Insert("bs_customers_bwd", $customer_data)) {
            throw new Exception("Failed to insert customer");
        }

        $customer_id = (int)$dbc->GetID();
    }

    $orderableType = isset($data['orderable_type']) ? trim($data['orderable_type']) : '';
    $postalCode = extractPostalCode($shipping_address);
    $isRemote = isRemoteArea($postalCode);

    if (isset($data['remote_area_fee'])) {
        $isRemote = ((int)$data['remote_area_fee'] > 0);
    }

    $is_discount_platform = isDiscountPlatform($data['platform']);


    if (($orderableType === 'receive_at_company' || $orderableType === 'receive_at_luckgems')) {
        if (isset($data['boxes']) && is_array($data['boxes']) && count($data['boxes']) > 1) {
            $mergedItems = [];
            $mergedTotal = 0;

            foreach ($data['boxes'] as $box) {
                if (isset($box['items']) && is_array($box['items'])) {
                    $mergedItems = array_merge($mergedItems, $box['items']);
                }
                if (isset($box['total'])) {
                    $mergedTotal += $box['total'];
                }
            }

            $data['boxes'] = [
                [
                    'items' => $mergedItems,
                    'total' => $mergedTotal
                ]
            ];

            error_log("✅ Merged boxes for pickup order: " . count($mergedItems) . " items");
        }
    }

    $boxes = [];
    if (isset($data['boxes']) && is_array($data['boxes']) && count($data['boxes']) > 0) {
        $boxes = $data['boxes'];
        error_log("📦 Using boxes from frontend: " . count($boxes) . " boxes");
    } else {
        $total = 0;
        foreach ($data['items'] as $item) {
            $amount = (float)$item['amount'];
            $price = (float)$item['price'];
            $discountSel = isset($item['discount']) ? (string)$item['discount'] : "0";

            $item_total = $amount * $price;
            $discount = 0.0;
            if ($discountSel === "5") $discount = $item_total * 0.05;
            elseif ($discountSel === "10") $discount = $item_total * 0.10;
            elseif ($discountSel === "15") $discount = $item_total * 0.15;
            elseif ($discountSel === "20") $discount = $item_total * 0.20;
            elseif ($discountSel === "25") $discount = $item_total * 0.25;
            elseif ($discountSel === "30") $discount = $item_total * 0.30;

            $total += ($item_total - $discount);
        }

        $boxes = [
            [
                'items' => $data['items'],
                'total' => $total
            ]
        ];
        error_log("📦 Created single box with total: " . $total);
    }

    $order_date_input = isset($data['date']) ? trim($data['date']) : '';
    $delivery_date_input = isset($data['delivery_date']) ? trim($data['delivery_date']) : '';

    $fee = 0.0;
    if (isset($data['fee']) && is_numeric($data['fee'])) {
        $fee = toDec4($data['fee']);
    }

    $main_order_id = null;
    $order_code = "";
    $total_net = 0.0;
    $box_number = 0;
    $shipping_per_box = [];

    foreach ($boxes as $box) {
        $boxItems = $box['items'];
        $boxTotal = $box['total'];
        $itemIndex = 0;

        error_log("=== Processing Box #" . ($box_number + 1) . " ===");
        error_log("Box items count: " . count($boxItems));
        error_log("Box total: " . $boxTotal);

        if (isset($data['shipping_breakdown']['shippingPerBox'][$box_number])) {
            $frontendShipping = $data['shipping_breakdown']['shippingPerBox'][$box_number];

            $boxShipping = [
                'base' => floatval($frontendShipping['base'] ?? 0),
                'box_fee' => floatval($frontendShipping['box_fee'] ?? 0),
                'remote_fee' => floatval($frontendShipping['remote_fee'] ?? 0),
                'total' => floatval($frontendShipping['total'] ?? 0),
                'wooden_count' => floatval($frontendShipping['wooden_count'] ?? 0),
                'premium_count' => floatval($frontendShipping['premium_count'] ?? 0)
            ];
            error_log("Using frontend shipping data");
        } else {
            $boxShipping = calculateShippingPerBox(
                $boxItems,
                $boxTotal,
                $box_number,
                $isRemote,
                $orderableType
            );
            error_log("Calculated shipping data");
        }

        $shipping_per_box[$box_number] = $boxShipping;

        foreach ($boxItems as $item) {
            error_log("--- Item #" . ($itemIndex + 1) . " in Box #" . ($box_number + 1) . " ---");

            $amount = (float)$item['amount'];
            $price = (float)$item['price'];
            $discountSel = isset($item['discount']) ? (string)$item['discount'] : "0";

            $ai_value = isset($item['ai']) ? trim((string)$item['ai']) : '0';

            error_log("AI Value from item: '" . $ai_value . "' (type: " . gettype($ai_value) . ")");
            error_log("Full item data: " . json_encode($item, JSON_UNESCAPED_UNICODE));

            $total = $amount * $price;
            $discount = 0.0;
            if ($discountSel === "5") $discount = $total * 0.05;
            elseif ($discountSel === "10") $discount = $total * 0.10;
            elseif ($discountSel === "15") $discount = $total * 0.15;
            elseif ($discountSel === "20") $discount = $total * 0.20;
            elseif ($discountSel === "25") $discount = $total * 0.25;
            elseif ($discountSel === "30") $discount = $total * 0.30;

            $ai_fee = 0.0;
            $ai_int = 0;

            if ($ai_value === "1" || $ai_value === 1 || $ai_value === '1') {
                $ai_int = 1;
                if (!$is_discount_platform) {
                    $ai_fee = 400.0 * $amount;
                    error_log("✅ AI Fee calculated: " . $ai_fee . " (amount: " . $amount . " x 400)");
                } else {
                    error_log("ℹ️ AI Fee skipped (discount platform)");
                }
            } else {
                error_log("ℹ️ No AI Fee (ai value: '" . $ai_value . "')");
            }

            $engrave_fee = (isset($item['engrave']) && $item['engrave'] === "สลักข้อความบนแท่งเงิน")
                ? ($is_discount_platform ? 0.0 : 300.0 * $amount)
                : 0.0;

            $ship_each = 0;
            if ($itemIndex === 0) {
                $ship_each = $boxShipping['total'];
            }

            $net = $total - $discount + $ship_each + $ai_fee + $engrave_fee;
            $total_net += $net;


            $order_data = [
                '#id' => "DEFAULT",
                '#customer_id' => $customer_id,
                "customer_name" => $data['customer_name'],
                "phone" => $data['phone'] ?? null,
                "platform" => $data['platform'],
                "order_platform" => $order_platform,
                "#sales" => $os->auth['id'],
                "#user" => $os->auth['id'],
                '#type' => 1,
                "#parent" => ($box_number === 0 && $itemIndex === 0) ? 'NULL' : $main_order_id,
                '#created' => 'NOW()',
                '#updated' => 'NOW()',
                '#amount' => $amount,
                '#price' => $price,
                '#vat_type' => (int)$data['vat_type'],
                '#discount_type' => (int)$discountSel,
                '#discount' => $discount,
                '#total' => $total,
                '#net' => $net,
                "#status" => 1,
                'comment' => $data['comment'] ?? "",
                'shipping_address' => $shipping_address,
                'billing_address' => $billing_address,
                '#shipping' => ($box_number === 0 && $itemIndex === 0 && isset($data['shipping']))
                    ? (int)$data['shipping']
                    : "NULL",
                '#shipping_base' => ($itemIndex === 0) ? $boxShipping['base'] : 0,
                '#shipping_box_fee' => ($itemIndex === 0) ? $boxShipping['box_fee'] : 0,
                '#shipping_remote_fee' => ($itemIndex === 0) ? $boxShipping['remote_fee'] : 0,
                '#shipping_total' => ($itemIndex === 0) ? $boxShipping['total'] : 0,
                "engrave" => $item['engrave'] ?? "ไม่สลักข้อความบนแท่งเงิน",
                "#ai" => $ai_int, 
                "font" => $item['font'] ?? "",
                "carving" => $item['carving'] ?? "",
                '#product_type' => (int)$item['product_type'],
                '#product_id' => (int)$item['product_id'],
                '#delivery_pack' => 0,
                '#box_number' => $box_number,
                "orderable_type" => $orderableType
            ];

            error_log("📝 Inserting order with AI: " . $ai_int);

            setDateTimeRaw($order_data, 'date', $order_date_input);

            if (isset($data['delivery_lock']) || $delivery_date_input === '') {
                $order_data['#delivery_date'] = "NULL";
            } else {
                setDateRaw($order_data, 'delivery_date', $delivery_date_input);
            }

            if ($box_number === 0 && $itemIndex === 0) {
                $order_data['#fee'] = $fee;
            }

            if (!$dbc->Insert("bs_orders_bwd", $order_data)) {
                throw new Exception("Insert Error for box " . ($box_number + 1) . ", item " . ($itemIndex + 1));
            }

            $current_order_id = (int)$dbc->GetID();

            $saved_order = $dbc->GetRecord("bs_orders_bwd", "ai, net", "id=" . $current_order_id);
            error_log("Saved Order #" . $current_order_id .
                " - AI: " . $saved_order['ai'] .
                ", Net: " . $saved_order['net'] .
                " (Expected AI: " . $ai_int . ", Expected AI Fee: " . $ai_fee . ")");

            if ($box_number === 0 && $itemIndex === 0) {
                $main_order_id = $current_order_id;
                $order_code = "OD-" . sprintf("%07s", $current_order_id);
                $dbc->Update("bs_orders_bwd", ["code" => $order_code], "id=" . $current_order_id);
                error_log("✅ Main order created: " . $order_code);
            }

            $order_logged = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $current_order_id);
            $os->save_log(0, $_SESSION['auth']['user_id'], "bwd2-multi-order-add", $current_order_id, ["bwd-orders2" => $order_logged]);

            $itemIndex++;
        }
        $box_number++;
    }

    $delivery_id = null;
    $delivery_code = null;

    if (isset($data['orderable_type']) && !empty($data['orderable_type'])) {
        $delivery_comment = "Order: " . $order_code . " | ";
        $delivery_comment .= "จำนวนกล่อง: " . count($boxes) . " | ";

        foreach ($shipping_per_box as $box_idx => $box_ship) {
            $delivery_comment .= sprintf(
                "กล่อง %d: %s บาท (ฐาน:%s+กล่อง:%s+ห่างไกล:%s) | ",
                $box_idx + 1,
                number_format($box_ship['total'], 2),
                number_format($box_ship['base'], 2),
                number_format($box_ship['box_fee'], 2),
                number_format($box_ship['remote_fee'], 2)
            );
        }

        $total_shipping = 0;
        foreach ($shipping_per_box as $ship) {
            $total_shipping += $ship['total'];
        }
        $delivery_comment .= "รวมค่าส่ง: " . number_format($total_shipping, 2) . " บาท";

        $delivery_data = [
            '#id' => 'DEFAULT',
            '#type' => 1,
            '#created' => 'NOW()',
            '#updated' => 'NOW()',
            '#status' => 1,
            '#amount' =>  $amount,
            '#user' => $os->auth['id'],
            'comment' => $delivery_comment,
            'delivery_method' => $data['orderable_type']
        ];

        if (!$dbc->Insert("bs_deliveries_bwd", $delivery_data)) {
            throw new Exception("Failed to insert delivery");
        }

        $delivery_id = (int)$dbc->GetID();
        $delivery_code = "DB-" . sprintf("%07s", $delivery_id);

        $dbc->Update(
            "bs_deliveries_bwd",
            ["code" => $delivery_code],
            "id=" . $delivery_id
        );

        $dbc->Query(
            "UPDATE bs_orders_bwd 
             SET delivery_id = " . $delivery_id . " 
             WHERE id = " . $main_order_id . " OR parent = " . $main_order_id
        );

        error_log("✅ Delivery created: " . $delivery_code);
    }

    echo json_encode([
        'success' => true,
        'msg' => 'Order created successfully!',
        'order_id' => $main_order_id,
        'order_code' => $order_code,
        'customer_id' => $customer_id,
        'total_net' => $total_net,
        'num_boxes' => count($boxes),
        'shipping_per_box' => $shipping_per_box,
        'delivery_id' => $delivery_id,
        'delivery_code' => $delivery_code,
        'is_remote' => $isRemote,
        'postal_code' => $postalCode
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ]);
}

$dbc->Close();
