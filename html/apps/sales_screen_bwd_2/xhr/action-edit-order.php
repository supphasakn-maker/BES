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

// ตรวจสอบ POST data
if (!isset($_POST['main_order_id']) || empty($_POST['main_order_id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => "ไม่พบ ID ของออเดอร์หลัก"
    ));
    exit;
}

$main_order_id = intval($_POST['main_order_id']);

// ตรวจสอบว่า main order มีอยู่จริง
$main_order = $dbc->GetRecord("bs_orders_bwd_2", "*", "id=" . $main_order_id);
if (!$main_order) {
    echo json_encode(array(
        'success' => false,
        'msg' => "ไม่พบออเดอร์หลักที่ต้องการแก้ไข"
    ));
    exit;
}

// Validate customer data
if (empty($_POST['customer_name']) || empty($_POST['platform'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => "กรุณากรอกข้อมูลลูกค้าให้ครบถ้วน"
    ));
    exit;
}

// ตรวจสอบข้อมูล orders
if (!isset($_POST['orders']) || !is_array($_POST['orders'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => "ไม่พบข้อมูลรายการสินค้า"
    ));
    exit;
}

try {
    // Start transaction
    $dbc->query("START TRANSACTION");

    $updated_orders = array();
    $total_net = 0;

    // อัปเดตแต่ละ order
    foreach ($_POST['orders'] as $index => $order_data) {
        $order_id = intval($order_data['id']);
        $is_main = intval($order_data['is_main']) === 1;

        // ตรวจสอบว่า order มีอยู่จริง
        $existing_order = $dbc->GetRecord("bs_orders_bwd_2", "*", "id=" . $order_id);
        if (!$existing_order) {
            throw new Exception("ไม่พบออเดอร์ ID: " . $order_id);
        }

        // Validate order data
        $amount = floatval($order_data['amount'] ?? 0);
        $price = floatval($order_data['price'] ?? 0);
        $product_id = intval($order_data['product_id'] ?? 0);
        $product_type = intval($order_data['product_type'] ?? 0);

        if ($amount <= 0 || $price <= 0 || $product_id <= 0 || $product_type <= 0) {
            throw new Exception("ข้อมูลรายการที่ " . ($index + 1) . " ไม่ครบถ้วน");
        }

        // คำนวณยอดเงิน
        $discount_type = intval($order_data['discount_type'] ?? 0);
        $ai = intval($order_data['ai'] ?? 0);
        $engrave = $order_data['engrave'] ?? 'ไม่สลักข้อความบนแท่งเงิน';

        // คำนวณ total
        $total = $amount * $price;

        // คำนวณส่วนลด
        $discount = 0;
        if ($discount_type > 0) {
            $discount = $total * ($discount_type / 100);
        }

        // คำนวณค่า AI
        $ai_cost = 0;
        if ($ai == 1) {
            $ai_cost = $amount * 200;
        }

        // คำนวณค่าสลักข้อความ
        $engrave_cost = 0;
        if ($engrave === 'สลักข้อความบนแท่งเงิน') {
            $engrave_cost = $amount * 100;
        }

        // คำนวณยอดสุทธิ (สำหรับ main order เพิ่มค่าส่งด้วย)
        $net = $total - $discount + $ai_cost + $engrave_cost;
        if ($is_main && isset($_POST['shipping'])) {
            $shipping_cost = 0;
            $shipping_method = intval($_POST['shipping']);
            if ($shipping_method == 1) {
                $shipping_cost = 50;
            } elseif ($shipping_method == 2) {
                $shipping_cost = 100;
            } elseif ($shipping_method == 3) {
                $shipping_cost = 150;
            }
            $net += $shipping_cost;
        }

        $total_net += $net;

        // เตรียมข้อมูลสำหรับอัปเดต
        $update_data = array(
            '#amount' => $amount,
            '#price' => $price,
            '#discount_type' => $discount_type,
            '#discount' => $discount,
            '#total' => $total,
            '#net' => $net,
            '#product_id' => $product_id,
            '#product_type' => $product_type,
            'engrave' => $engrave,
            'font' => $order_data['font'] ?? null,
            'carving' => $order_data['carving'] ?? null,
            '#ai' => $ai,
            '#updated' => 'NOW()'
        );

        // อัปเดตข้อมูลลูกค้าและข้อมูลทั่วไปเฉพาะ main order
        if ($is_main) {
            $update_data['customer_name'] = $_POST['customer_name'];
            $update_data['phone'] = $_POST['phone'] ?? null;
            $update_data['platform'] = $_POST['platform'];
            $update_data['delivery_date'] = $_POST['delivery_date'] ?? null;
            $update_data['#shipping'] = intval($_POST['shipping'] ?? 0);
            $update_data['shipping_address'] = $_POST['shipping_address'] ?? null;
            $update_data['billing_address'] = $_POST['billing_address'] ?? null;
            $update_data['comment'] = $_POST['comment'] ?? null;
            $update_data['Tracking'] = $_POST['Tracking'] ?? null;
        }

        // อัปเดตออเดอร์
        if ($dbc->Update("bs_orders_bwd_2", $update_data, "id=" . $order_id)) {
            $updated_orders[] = array(
                'id' => $order_id,
                'is_main' => $is_main,
                'net' => $net
            );
        } else {
            throw new Exception("ไม่สามารถอัปเดตรายการที่ " . ($index + 1) . " ได้");
        }
    }

    // อัปเดต customer data ถ้ามี
    if (!empty($_POST['phone'])) {
        $customer_search_condition = "phone = '" . addslashes($_POST['phone']) . "'";
        $existing_customer = $dbc->GetRecord("bs_customers_bwd", "*", $customer_search_condition);

        if ($existing_customer) {
            $customer_update_data = array(
                'customer_name' => $_POST['customer_name'],
                'shipping_address' => $_POST['shipping_address'] ?? null,
                'billing_address' => $_POST['billing_address'] ?? null,
                '#updated' => 'NOW()'
            );
            $dbc->Update("bs_customers_bwd", $customer_update_data, "id=" . $existing_customer['id']);
        }
    }

    // Commit transaction
    $dbc->query("COMMIT");

    // บันทึก log สำหรับแต่ละ order ที่อัปเดต
    try {
        $user_id = $os->auth['id'] ?? 0;
        foreach ($updated_orders as $updated_order) {
            $order_data = $dbc->GetRecord("bs_orders_bwd_2", "*", "id=" . $updated_order['id']);
            $os->save_log(0, $user_id, "order-bwd-edit", $updated_order['id'], array("bwd-orders" => $order_data));
        }
    } catch (Exception $log_error) {
        // Log error แต่ไม่ fail การอัปเดต
        error_log("Log save error: " . $log_error->getMessage());
    }

    $main_order_updated = $dbc->GetRecord("bs_orders_bwd_2", "*", "id=" . $main_order_id);

    echo json_encode(array(
        'success' => true,
        'msg' => "แก้ไขข้อมูลออเดอร์สำเร็จ (" . count($updated_orders) . " รายการ)",
        'main_order_id' => $main_order_id,
        'order_code' => $main_order_updated['code'],
        'updated_count' => count($updated_orders),
        'total_net' => $total_net
    ));
} catch (Exception $e) {
    // Rollback transaction
    $dbc->query("ROLLBACK");

    echo json_encode(array(
        'success' => false,
        'msg' => "เกิดข้อผิดพลาด: " . $e->getMessage()
    ));
}

$dbc->Close();
