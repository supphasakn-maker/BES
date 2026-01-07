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

$total_order = 0;
$total_total = 0;

for ($i = 0; $i < count($_POST['order_id']); $i++) {
    $total_order += $_POST['order_amount'][$i];
    $total_total += $_POST['order_total'][$i];
}

if (count($_POST['order_id']) < 1) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'There is no order item.'
    ));
} else if ($_POST['date'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input date.'
    ));
} else {

    $data = array(
        "#id" => 'DEFAULT',
        "mapped" => $_POST['date'] . " " . date("H:i:s"),
        "#amount" => $total_order,
        "#total" => $total_total,
        "remark" => $_POST['remark']
    );
    if ($dbc->Insert("bs_mapping_profit", $data)) {
        $mapping_id = $dbc->GetID();

        // Loop for ORDER
        for ($i = 0; $i < count($_POST['order_id']); $i++) {
            $order = $dbc->GetRecord("bs_orders_profit", "*", "order_id=" . $_POST['order_id'][$i]);
            $data = array(
                "#id" => 'DEFAULT',
                "#mapping_id" => $mapping_id,
                "#order_id" => $_POST['order_id'][$i],
                "#amount" => $_POST['order_amount'][$i],
                "#total" => $_POST['order_total'][$i],
            );
            // Case : No Matching Before
            if ($_POST['order_mapping_id'][$i] == "") {
                $dbc->Insert("bs_mapping_profit_orders", $data);
                if ($order['amount'] > $_POST['order_amount'][$i]) {
                    $data["#amount"] = $order['amount'] - $_POST['order_amount'][$i];
                    $data["#total"] = $order['total'] - $_POST['order_total'][$i];
                    $data["#mapping_id"] = "NULL";

                    // แก้ไข: ใช้ bs_mapping_profit_orders แทน bs_mapping_silver_orders
                    $dbc->Insert("bs_mapping_profit_orders", $data);
                }
                // Case : Already Match Some
            } else {
                $mapping = $dbc->GetRecord("bs_mapping_profit_orders", "*", "id=" . $_POST['order_mapping_id'][$i]);
                if ($mapping['amount'] > $_POST['order_amount'][$i]) {
                    $data["#amount"] = $mapping['amount'] - $_POST['order_amount'][$i];
                    $data["#total"] = $mapping['amount'] > $_POST['order_amount'][$i] ? $mapping['total'] - $_POST['order_total'][$i] : 0;
                    $data["#mapping_id"] = "NULL";
                    $dbc->Insert("bs_mapping_profit_orders", $data);
                }

                $dbc->Update("bs_mapping_profit_orders", array(
                    "#amount" => $_POST['order_amount'][$i],
                    "#total" => $_POST['order_total'][$i],
                    "#mapping_id" => $mapping_id
                ), "id=" . $_POST['order_mapping_id'][$i]);
            }
        }
        echo json_encode(array(
            'success' => true,
            'msg' => $mapping_id
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
