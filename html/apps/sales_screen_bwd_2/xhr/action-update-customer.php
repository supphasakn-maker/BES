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

header('Content-Type: application/json');

if (!isset($_POST['data'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'No data received'
    ));
    exit;
}

$data = json_decode($_POST['data'], true);
if (!$data) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Invalid JSON data'
    ));
    exit;
}

if (empty($data['id'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Customer ID is required'
    ));
    exit;
}

if (empty($data['customer_name'])) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Customer name is required'
    ));
    exit;
}

try {
    $customer_id = (int)$data['id'];

    // ตรวจสอบว่าลูกค้าอยู่จริง
    $sql = "SELECT * FROM bs_customers_bwd WHERE id = $customer_id LIMIT 1";
    $result = $dbc->query($sql);

    if (!$result || $result->num_rows == 0) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Customer not found'
        ));
        exit;
    }

    $existing_customer = $result->fetch_assoc();

    // อัปเดตข้อมูล
    $customer_name_escaped = addslashes($data['customer_name']);
    $shipping_address_escaped = addslashes($data['shipping_address'] ?? '');
    $billing_address_escaped = addslashes($data['billing_address'] ?? '');

    $update_sql = "UPDATE bs_customers_bwd SET 
                   customer_name = '$customer_name_escaped',
                   shipping_address = '$shipping_address_escaped',
                   billing_address = '$billing_address_escaped',
                   updated = NOW()
                   WHERE id = $customer_id";

    if ($dbc->query($update_sql)) {
        // Log การแก้ไข (ถ้าต้องการ)
        if (isset($_SESSION['auth']['user_id'])) {
            $os->save_log(0, $_SESSION['auth']['user_id'], "customer-update", $customer_id, array(
                "old_data" => $existing_customer,
                "new_data" => $data
            ));
        }

        echo json_encode(array(
            'success' => true,
            'msg' => 'Customer updated successfully',
            'customer_id' => $customer_id
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Failed to update customer'
        ));
    }
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ));
}

$dbc->Close();
