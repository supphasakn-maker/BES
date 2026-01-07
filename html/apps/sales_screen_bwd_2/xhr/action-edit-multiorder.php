<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

function isDiscountPlatform($platform)
{
    $discountPlatforms = array('Shopee', 'Lazada', 'TikTok');
    return in_array($platform, $discountPlatforms);
}

if (!isset($_POST['main_order_id']) || empty($_POST['main_order_id'])) {
    echo json_encode(array('success' => false, 'msg' => "ไม่พบ ID ของออเดอร์หลัก"));
    exit;
}

$main_order_id = intval($_POST['main_order_id']);

$main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order_id);
if (!$main_order) {
    echo json_encode(array('success' => false, 'msg' => "ไม่พบออเดอร์หลักที่ต้องการแก้ไข"));
    exit;
}

if (empty($_POST['customer_name']) || empty($_POST['platform'])) {
    echo json_encode(array('success' => false, 'msg' => "กรุณากรอกข้อมูลลูกค้าให้ครบถ้วน"));
    exit;
}

if (!isset($_POST['orders']) || !is_array($_POST['orders'])) {
    echo json_encode(array('success' => false, 'msg' => "ไม่พบข้อมูลรายการสินค้า"));
    exit;
}

$fee = 0.0000;
if (isset($_POST['fee']) && $_POST['fee'] !== '') {
    $fee = floatval($_POST['fee']);
    if ($fee < 0) $fee = 0.0000;
}
$fee = (float)number_format($fee, 4, '.', '');

try {
    $dbc->query("START TRANSACTION");

    $updated_orders = array();
    $total_net = 0.0;
    $main_order_net_before_fee = null;

    $current_platform = $_POST['platform'];
    $is_discount_platform = isDiscountPlatform($current_platform);

    foreach ($_POST['orders'] as $index => $order_data) {
        $order_id   = intval($order_data['id']);
        $is_main    = intval($order_data['is_main']) === 1;

        $existing_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);
        if (!$existing_order) {
            throw new Exception("ไม่พบออเดอร์ ID: " . $order_id);
        }

        $amount       = floatval($order_data['amount'] ?? 0);
        $price        = floatval($order_data['price'] ?? 0);
        $product_id   = intval($order_data['product_id'] ?? 0);
        $product_type = intval($order_data['product_type'] ?? 0);

        if ($amount <= 0 || $price <= 0 || $product_id <= 0 || $product_type <= 0) {
            throw new Exception("ข้อมูลรายการที่ " . ($index + 1) . " ไม่ครบถ้วน");
        }

        $discount_type = intval($order_data['discount_type'] ?? 0);
        $ai            = intval($order_data['ai'] ?? 0);
        $engrave       = $order_data['engrave'] ?? 'ไม่สลักข้อความบนแท่งเงิน';

        $total = $amount * $price;

        $discount = 0.0;
        if ($discount_type > 0) {
            $discount = $total * ($discount_type / 100);
        }

        $ai_cost = 0.0;
        if ($ai == 1) {
            $ai_cost = $is_discount_platform ? 0.0 : ($amount * 300);
        }

        $engrave_cost = 0.0;
        if ($engrave === 'สลักข้อความบนแท่งเงิน') {
            $engrave_cost = $is_discount_platform ? 0.0 : ($amount * 200);
        }

        $net = $total - $discount + $ai_cost + $engrave_cost;

        if ($is_main && isset($_POST['shipping'])) {
            $shipping_cost = 0.0;
            $shipping_method = intval($_POST['shipping']);
            if ($shipping_method == 1)      $shipping_cost = 50;
            elseif ($shipping_method == 2)  $shipping_cost = 100;
            elseif ($shipping_method == 3)  $shipping_cost = 150;
            $net += $shipping_cost;
        }

        $total_net += $net;

        if ($is_main) {
            $main_order_net_before_fee = $net;
        }

        // เตรียมข้อมูลอัปเดต
        $update_data = array(
            '#amount'        => $amount,
            '#price'         => $price,
            '#discount_type' => $discount_type,
            '#discount'      => $discount,
            '#total'         => $total,
            '#net'           => $net,
            '#product_id'    => $product_id,
            '#product_type'  => $product_type,
            'engrave'        => $engrave,
            'font'           => $order_data['font'] ?? null,
            'carving'        => $order_data['carving'] ?? null,
            'ai'             => $ai,
            '#updated'       => 'NOW()',
        );

        if ($is_main) {
            $update_data['customer_name']    = $_POST['customer_name'];
            $update_data['phone']            = $_POST['phone'] ?? null;
            $update_data['platform']         = $_POST['platform'];
            if (isset($_POST['vat_type']) && $_POST['vat_type'] !== '') {
                $update_data['#vat_type'] = intval($_POST['vat_type']);
            }
            $update_data['date']             = $_POST['date'] ?? null;
            $update_data['delivery_date']    = $_POST['delivery_date'] ?? null;
            $update_data['#shipping']        = intval($_POST['shipping'] ?? 0);
            $update_data['shipping_address'] = $_POST['shipping_address'] ?? null;
            $update_data['billing_address']  = $_POST['billing_address'] ?? null;
            $update_data['comment']          = $_POST['comment'] ?? null;
            $update_data['Tracking']         = $_POST['Tracking'] ?? null;
        } else {
            $update_data['platform'] = $_POST['platform'];
        }

        if ($dbc->Update("bs_orders_bwd", $update_data, "id=" . $order_id)) {
            $updated_orders[] = array(
                'id'           => $order_id,
                'is_main'      => $is_main,
                'net'          => $net,
                'ai_cost'      => $ai_cost,
                'engrave_cost' => $engrave_cost
            );
        } else {
            throw new Exception("ไม่สามารถอัปเดตรายการที่ " . ($index + 1) . " ได้");
        }
    }

    if ($main_order_net_before_fee === null) {
        throw new Exception("ไม่พบรายการหลักสำหรับการหักค่าธรรมเนียม");
    }
    $main_order_net_after_fee = (float)number_format(($main_order_net_before_fee - $fee), 4, '.', '');
    if ($main_order_net_after_fee < 0) $main_order_net_after_fee = 0.0000;

    $dbc->Update("bs_orders_bwd", array(
        '#fee' => $fee,
        '#net' => $main_order_net_after_fee
    ), "id=" . $main_order_id);

    $grand_total_after_fee = (float)number_format(($total_net - $fee), 4, '.', '');
    if ($grand_total_after_fee < 0) $grand_total_after_fee = 0.0000;

    if (isset($_POST['delivery_date']) && !empty($_POST['delivery_date'])) {
        $new_delivery_date = $_POST['delivery_date'];

        $main_order_updated = $dbc->GetRecord("bs_orders_bwd", "delivery_id", "id=" . $main_order_id);

        if ($main_order_updated && $main_order_updated['delivery_id']) {
            $delivery_id = $main_order_updated['delivery_id'];

            $delivery_update_data = array(
                'delivery_date' => $new_delivery_date,
                '#updated'      => 'NOW()',
                '#amount'       => $grand_total_after_fee
            );
            $dbc->Update("bs_deliveries_bwd", $delivery_update_data, "id=" . $delivery_id);
        } else {
            $delivery_insert_data = array(
                "#id"           => "DEFAULT",
                "#type"         => 1,
                'delivery_date' => $new_delivery_date,
                "#created"      => "NOW()",
                "#updated"      => "NOW()",
                "#status"       => 0,
                "#amount"       => $grand_total_after_fee,
                "#user"         => $os->auth['id'],
                'comment'       => $_POST['comment'] ?? ""
            );

            $json = array("bank" => '', "payment" => '', "remark" => '');
            $delivery_insert_data['payment_note'] = json_encode($json, JSON_UNESCAPED_UNICODE);

            if ($dbc->Insert("bs_deliveries_bwd", $delivery_insert_data)) {
                $new_delivery_id = $dbc->GetID();

                $delivery_code = "DB-" . sprintf("%07s", $new_delivery_id);
                $dbc->Update("bs_deliveries_bwd", array("code" => $delivery_code), "id=" . $new_delivery_id);

                $dbc->Update("bs_orders_bwd", array('delivery_id' => $new_delivery_id), "id=" . $main_order_id);
            }
        }
    }

    $dbc->query("COMMIT");

    try {
        $user_id = $os->auth['id'] ?? 0;
        foreach ($updated_orders as $updated_order) {
            $order_data = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $updated_order['id']);
            $os->save_log(0, $user_id, "order-bwd-edit", $updated_order['id'], array("bwd-orders" => $order_data));
        }
    } catch (Exception $log_error) {
        error_log("Log save error: " . $log_error->getMessage());
    }

    $main_order_after = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $main_order_id);

    $platform_info = $is_discount_platform ? " (Marketplace: no AI/engrave fee)" : " (Standard Platform)";

    echo json_encode(array(
        'success'               => true,
        'msg'                   => "แก้ไขข้อมูลออเดอร์สำเร็จ (" . count($updated_orders) . " รายการ)" . $platform_info,
        'main_order_id'         => $main_order_id,
        'order_code'            => $main_order_after['code'],
        'updated_count'         => count($updated_orders),
        // รายงานทั้งก่อน/หลังหัก fee
        'subtotal_before_fee'   => number_format($total_net, 4, '.', ''),
        'fee'                   => number_format($fee, 4, '.', ''),
        'total_after_fee'       => number_format($grand_total_after_fee, 4, '.', ''),
        'platform'              => $current_platform,
        'is_discount_platform'  => $is_discount_platform,
        'calculation_details'   => array(
            'total_ai_cost'      => array_sum(array_column($updated_orders, 'ai_cost')),
            'total_engrave_cost' => array_sum(array_column($updated_orders, 'engrave_cost'))
        )
    ));
} catch (Exception $e) {
    $dbc->query("ROLLBACK");
    echo json_encode(array('success' => false, 'msg' => "เกิดข้อผิดพลาด: " . $e->getMessage()));
}

$dbc->Close();
