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

try {
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
            // Update existing customer
            $customer_id = $existing_customer['id'];

            $update_data = array(
                'customer_name' => $data['customer_name'],
                'username' => isset($data['username']) ? $data['username'] : $existing_customer['username'],
                'phone' => isset($data['phone']) ? $data['phone'] : $existing_customer['phone'],
                'shipping_address' => isset($data['shipping_address']) ? $data['shipping_address'] : $existing_customer['shipping_address'],
                'billing_address' => isset($data['billing_address']) ? $data['billing_address'] : $existing_customer['billing_address'],
                '#updated' => 'NOW()'
            );

            $dbc->Update("bs_customers_bwd", $update_data, "id=" . $customer_id);
        } else {
            // Insert new customer
            $insert_data = array(
                '#id' => "DEFAULT",
                'customer_name' => $data['customer_name'],
                'username' => isset($data['username']) ? $data['username'] : null,
                'phone' => isset($data['phone']) ? $data['phone'] : null,
                'shipping_address' => isset($data['shipping_address']) ? $data['shipping_address'] : null,
                'billing_address' => isset($data['billing_address']) ? $data['billing_address'] : null,
                '#created' => 'NOW()',
                '#updated' => 'NOW()'
            );

            $dbc->Insert("bs_customers_bwd", $insert_data);
            $customer_id = $dbc->GetID();
        }

        echo json_encode(array(
            'success' => true,
            'customer_id' => $customer_id,
            'msg' => 'Customer saved successfully'
        ));
    } else {
        throw new Exception('No phone or username provided');
    }
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'msg' => $e->getMessage()
    ));
}

$dbc->Close();
