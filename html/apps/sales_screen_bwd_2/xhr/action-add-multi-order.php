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


foreach ($data['items'] as $index => $item) {
    if (empty($item['amount']) || (float)$item['amount'] == 0) {
        echo json_encode(['success' => false, 'msg' => 'Please input bars for item ' . ($index + 1)]);
        exit;
    }
    if (!isset($item['price']) || $item['price'] === '' || !is_numeric($item['price'])) {
        echo json_encode(['success' => false, 'msg' => 'Please input price for item ' . ($index + 1)]);
        exit;
    }
    if (empty($item['product_id'])) {
        echo json_encode(['success' => false, 'msg' => 'Please Select Product for item ' . ($index + 1)]);
        exit;
    }
    if (empty($item['product_type'])) {
        echo json_encode(['success' => false, 'msg' => 'Please Select Product Type for item ' . ($index + 1)]);
        exit;
    }
}


$shipping = 0;
if (isset($data['shipping'])) {
    if ($data['shipping'] == "1")      $shipping = 50;
    else if ($data['shipping'] == "2") $shipping = 100;
    else if ($data['shipping'] == "3") $shipping = 150;
    else if ($data['shipping'] == "4") $shipping = 0;
}


function isDiscountPlatform($platform)
{
    $discountPlatforms = array('Shopee', 'Lazada', 'TikTok');
    return in_array($platform, $discountPlatforms);
}


$fee = 0.0000;
if (isset($data['fee']) && $data['fee'] !== '' && is_numeric($data['fee'])) {
    $fee = (float)$data['fee'];
    if ($fee < 0) $fee = 0.0000;
}
$fee = (float)number_format($fee, 4, '.', '');

try {

    $dbc->query("START TRANSACTION");


    $customer_id = null;
    if (!empty($data['customer_name'])) {
        $search_condition = '';
        if (!empty($data['phone'])) {
            $phone_escaped = addslashes($data['phone']);
            $search_condition = "phone = '$phone_escaped'";
        } elseif (!empty($data['username'])) {
            $username_escaped = addslashes($data['username']);
            $search_condition = "username = '$username_escaped'";
        }

        if ($search_condition) {
            $existing_customer = $dbc->GetRecord("bs_customers_bwd", "*", $search_condition);

            if ($existing_customer) {

                $customer_id = $existing_customer['id'];
                $update_data = array(
                    'customer_name'    => $data['customer_name'],
                    'shipping_address' => isset($data['shipping_address']) ? $data['shipping_address'] : $existing_customer['shipping_address'],
                    'billing_address'  => isset($data['billing_address']) ? $data['billing_address']  : $existing_customer['billing_address'],
                    '#updated'         => 'NOW()'
                );
                $dbc->Update("bs_customers_bwd", $update_data, "id=" . (int)$customer_id);
            } else {

                $insert_customer_data = array(
                    '#id'               => "DEFAULT",
                    'customer_name'     => $data['customer_name'],
                    'username'          => isset($data['username']) ? $data['username'] : null,
                    'phone'             => isset($data['phone']) ? $data['phone'] : null,
                    'shipping_address'  => isset($data['shipping_address']) ? $data['shipping_address'] : null,
                    'billing_address'   => isset($data['billing_address']) ? $data['billing_address'] : null,
                    '#created'          => 'NOW()',
                    '#updated'          => 'NOW()'
                );
                $dbc->Insert("bs_customers_bwd", $insert_customer_data);
                $customer_id = $dbc->GetID();
            }
        }
    }


    $main_order_id = null;
    $order_code    = null;
    $total_net     = 0.0;
    $is_discount_platform = isDiscountPlatform($data['platform']);

    foreach ($data['items'] as $index => $item) {
        $amount      = (float)$item['amount'];
        $price       = (float)$item['price'];
        $discountSel = isset($item['discount']) ? (string)$item['discount'] : "0";


        $total = $amount * $price;


        $discount = 0.0;
        if ($discountSel === "5")  $discount = $total * 0.05;
        elseif ($discountSel === "10") $discount = $total * 0.10;
        elseif ($discountSel === "15") $discount = $total * 0.15;
        elseif ($discountSel === "20") $discount = $total * 0.20;
        elseif ($discountSel === "25") $discount = $total * 0.25;
        elseif ($discountSel === "30") $discount = $total * 0.30;


        $ai = 0.0;
        if (isset($item['ai']) && (string)$item['ai'] === "1") {
            $ai = $is_discount_platform ? 0.0 : (300.0 * $amount);
        }

        $engrave_fee = 0.0;
        if (isset($item['engrave']) && $item['engrave'] === "สลักข้อความบนแท่งเงิน") {
            $engrave_fee = $is_discount_platform ? 0.0 : (200.0 * $amount);
        }


        $item_shipping = ($index === 0) ? $shipping : 0.0;

        $net = $total - $discount + $item_shipping + $ai + $engrave_fee;
        $total_net += $net;

        $order_data = array(
            '#id'              => "DEFAULT",
            '#customer_id'     => $customer_id,
            "customer_name"    => $data['customer_name'],
            "phone"            => isset($data['phone']) ? $data['phone'] : null,
            "platform"         => $data['platform'],
            "date"             => $data['date'],
            "#sales"           => $os->auth['id'],
            "#user"            => $os->auth['id'],
            '#type'            => 1,
            "#parent"          => ($index === 0) ? 'NULL' : $main_order_id,
            '#created'         => 'NOW()',
            '#updated'         => 'NOW()',
            '#amount'          => $amount,
            '#price'           => $price,
            '#vat_type'        => $data['vat_type'],
            '#discount_type'   => $discountSel,
            '#discount'        => $discount,
            '#total'           => $total,
            '#net'             => $net,
            "#status"          => 1,
            'comment'          => isset($data['comment']) ? $data['comment'] : "",
            'shipping_address' => isset($data['shipping_address']) ? $data['shipping_address'] : null,
            'billing_address'  => isset($data['billing_address']) ? $data['billing_address'] : null,
            '#shipping'        => ($index === 0) ? (isset($data['shipping']) ? (int)$data['shipping'] : 'NULL') : 'NULL',
            "engrave"          => isset($item['engrave']) ? $item['engrave'] : "ไม่สลักข้อความบนแท่งเงิน",
            "ai"               => isset($item['ai']) ? $item['ai'] : "0",
            "font"             => isset($item['font']) ? $item['font'] : "",
            "carving"          => isset($item['carving']) ? $item['carving'] : "",
            '#product_type'    => $item['product_type'],
            '#product_id'      => $item['product_id'],
            '#delivery_pack'   => 0
        );


        if ($index === 0) {
            $order_data['#fee'] = $fee;
        }


        if (isset($data['delivery_lock']) || empty($data['delivery_date'])) {
            $order_data['#delivery_date'] = "NULL";
        } else {
            $order_data['delivery_date'] = $data['delivery_date'];
        }


        if ($dbc->Insert("bs_orders_bwd", $order_data)) {
            $current_order_id = $dbc->GetID();


            if ($index === 0) {
                $main_order_id = $current_order_id;
                $order_code    = "OD-" . sprintf("%07s", $current_order_id);
                $dbc->Update("bs_orders_bwd", array("code" => $order_code), "id=" . (int)$current_order_id);
            }


            $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . (int)$current_order_id);
            $os->save_log(0, $_SESSION['auth']['user_id'], "bwd-multi-order-add", (int)$current_order_id, array("bwd-orders" => $order));
        } else {
            throw new Exception("Insert Error for item " . ($index + 1));
        }
    }




    if ($main_order_id) {
        $parent_now = $dbc->GetRecord("bs_orders_bwd", "id, net", "id=" . (int)$main_order_id);
        if (!$parent_now) {
            throw new Exception("Parent order not found after insert");
        }

        $parent_net_before = (float)$parent_now['net'];
        $parent_net_after  = (float)number_format(($parent_net_before - $fee), 4, '.', '');

        $dbc->Update("bs_orders_bwd", array(
            '#net' => $parent_net_after
        ), "id=" . (int)$main_order_id);
    }



    $grand_total_after_fee = (float)number_format(($total_net - $fee), 4, '.', '');

    $main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . (int)$main_order_id);
    if ($main_order && !is_null($main_order['delivery_date'])) {
        $delivery_data = array(
            "#id"           => "DEFAULT",
            "#type"         => 1,
            "delivery_date" => $main_order['delivery_date'],
            "#created"      => "NOW()",
            "#updated"      => "NOW()",
            "#status"       => 0,
            "#amount"       => $grand_total_after_fee,
            "#user"         => $os->auth['id'],
            "comment"       => isset($data['comment']) ? $data['comment'] : ""
        );

        $json = array("bank" => '', "payment" => '', "remark" => '');
        $delivery_data['payment_note'] = json_encode($json, JSON_UNESCAPED_UNICODE);

        $dbc->Insert("bs_deliveries_bwd", $delivery_data);
        $delivery_id = $dbc->GetID();

        $delivery_code = "DB-" . sprintf("%07s", $delivery_id);
        $dbc->Update("bs_deliveries_bwd", array("code" => $delivery_code), "id=" . (int)$delivery_id);


        $dbc->Update("bs_orders_bwd", array("delivery_id" => $delivery_id), "id=" . (int)$main_order_id);
    }


    $dbc->query("COMMIT");

    $platform_info = $is_discount_platform ? " (Marketplace: no AI/engrave fee)" : " (Standard Platform)";
    echo json_encode(array(
        'success'               => true,
        'msg'                   => "Your Multi-Order ID " . $order_code . " was created with " . count($data['items']) . " items!" . $platform_info,
        'order_id'              => $main_order_id,
        'order_code'            => $order_code,
        'customer_id'           => $customer_id,
        'total_items'           => count($data['items']),


        'subtotal_before_fee'   => number_format($total_net, 4, '.', ''),


        'fee'                   => number_format($fee, 4, '.', ''),
        'total_amount'          => $grand_total_after_fee,

        'platform'              => $data['platform'],
        'is_discount_platform'  => $is_discount_platform
    ));
} catch (Exception $e) {
    $dbc->query("ROLLBACK");
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}

$dbc->Close();
