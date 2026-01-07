<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

header('Content-Type: application/json');

try {
    if (empty($_POST['search'])) {
        echo json_encode(array(
            'success' => false,
            'msg' => 'Please input search term'
        ));
        exit;
    }

    $search = trim($_POST['search']);
    $search_escaped = addslashes($search);

    // ใช้ SQL Query โดยตรง
    $sql = "SELECT * FROM bs_customers_bwd 
            WHERE phone = '$search_escaped' OR username = '$search_escaped' 
            ORDER BY updated DESC, created DESC 
            LIMIT 1";

    $result = $dbc->query($sql);

    if ($result && $result->num_rows > 0) {
        $customer = $result->fetch_assoc();

        echo json_encode(array(
            'success' => true,
            'found' => true,
            'customer' => array(
                'id' => $customer['id'] ?? '',
                'customer_name' => $customer['customer_name'] ?? '',
                'username' => $customer['username'] ?? '',
                'phone' => $customer['phone'] ?? '',
                'shipping_address' => $customer['shipping_address'] ?? '',
                'billing_address' => $customer['billing_address'] ?? ''
            )
        ));
    } else {
        echo json_encode(array(
            'success' => true,
            'found' => false,
            'msg' => 'Customer not found. New customer will be created.'
        ));
    }
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Error: ' . $e->getMessage()
    ));
}

$dbc->Close();
