<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

// ตรวจสอบ POST data
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => "ไม่พบ ID ของออเดอร์"
    ));
    exit;
}

$order_id = intval($_POST['id']);

try {
    // ดึงข้อมูล order
    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order_id);

    if (!$order) {
        throw new Exception("ไม่พบออเดอร์ ID: " . $order_id);
    }

    // กำหนด main order
    $main_order = null;
    $sub_orders = array();

    if (is_null($order['parent'])) {
        // ถ้าเป็น main order
        $main_order = $order;

        // หา sub orders
        $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $order_id . " AND status > 0 ORDER BY id";
        $result = $dbc->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sub_orders[] = $row;
            }
        }
    } else {
        // ถ้าเป็น sub order ให้หา main order
        $main_order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $order['parent']);

        if ($main_order) {
            // หา sub orders ทั้งหมด
            $sql = "SELECT * FROM bs_orders_bwd WHERE parent = " . $main_order['id'] . " AND status > 0 ORDER BY id";
            $result = $dbc->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $sub_orders[] = $row;
                }
            }
        }
    }

    // ดึงข้อมูล products
    $products = array();
    $sql = "SELECT * FROM bs_products_bwd WHERE status = 1 ORDER BY name";
    $result = $dbc->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    // ดึงข้อมูล fonts
    $fonts = array();
    $sql = "SELECT * FROM bs_fonts_bwd WHERE status = 1 ORDER BY name";
    $result = $dbc->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fonts[] = $row;
        }
    }

    // ดึงข้อมูล shipping
    $shipping_methods = array();
    $sql = "SELECT * FROM bs_shipping_bwd WHERE status = 1 ORDER BY FIELD(id,4,1,2,3,5,6)";
    $result = $dbc->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $shipping_methods[] = $row;
        }
    }

    echo json_encode(array(
        'success' => true,
        'data' => array(
            'main_order' => $main_order,
            'sub_orders' => $sub_orders,
            'products' => $products,
            'fonts' => $fonts,
            'shipping_methods' => $shipping_methods
        )
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => $e->getMessage()
    ));
}

$dbc->Close();
