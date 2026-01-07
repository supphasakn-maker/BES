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

if ($_POST['customer_name'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input customer'
    ));
} else if ($_POST['platform'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please Select Platform'
    ));
} else if ($_POST['phone'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input Phone!!'
    ));
} else if ($_POST['amount'] == "" || $_POST['amount'] == 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input bars'
    ));
} else if ($_POST['price'] == "" || $_POST['price'] == 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input price'
    ));
} else if ($_POST['product_id'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please Select Product!!'
    ));
} else if ($_POST['product_type'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please Select Product Type!!'
    ));
} else {
    $total = $_POST['amount'] * $_POST['price'];

    if ($_POST['discount_type'] == "5") {
        $discount = $total * 5 / 100;
    } else if ($_POST['discount_type'] == "10") {
        $discount = $total * 10 / 100;
    } else if ($_POST['discount_type'] == "15") {
        $discount = $total * 15 / 100;
    } else if ($_POST['discount_type'] == "20") {
        $discount = $total * 20 / 100;
    } else if ($_POST['discount_type'] == "25") {
        $discount = $total * 25 / 100;
    } else if ($_POST['discount_type'] == "30") {
        $discount = $total * 30 / 100;
    } else {
        $discount = 0;
    }
    if ($_POST['shipping'] == "1") {
        $shipping = 50;
    } else if ($_POST['shipping'] == "2") {
        $shipping = 100;
    } else if ($_POST['shipping'] == "3") {
        $shipping = 150;
    } else if ($_POST['shipping'] == "4") {
        $shipping = 0;
    }

    if ($_POST['ai'] == "1") {
        $ai = 200 * $_POST['amount'];
    } else {
        $ai = 0;
    }
    if ($_POST['engrave'] == "สลักข้อความบนแท่งเงิน") {
        $engrave_fee = 100 * $_POST['amount'];
    } else {
        $engrave_fee = 0;
    }


    $net = $total - $discount + $shipping + $ai + $engrave_fee;

    $data = array(
        "customer_name" => $_POST['customer_name'],
        "phone" => $_POST['phone'],
        "platform" => $_POST['platform'],
        "date" => $_POST['date'],
        "#sales" => $os->auth['id'],
        "#user" => $os->auth['id'],
        '#type' => 1,
        '#updated' => 'NOW()',
        '#amount' => $_POST['amount'],
        '#price' => $_POST['price'],
        '#discount_type' => $_POST['discount_type'],
        '#discount' => $discount,
        '#total' => $total,
        '#net' => $net,
        'comment' => isset($_POST['comment']) ? $_POST['comment'] : "",
        'shipping_address' => $_POST['shipping_address'],
        'billing_address' => $_POST['billing_address'],
        'shipping' => $_POST['shipping'],
        "engrave" =>  $_POST['engrave'],
        "ai" =>  isset($_POST['ai']) ? $_POST['ai'] : "0",
        "font" =>  isset($_POST['font']) ? $_POST['font'] : "",
        "carving" =>  isset($_POST['carving']) ? $_POST['carving'] : "",
        '#product_type' => $_POST['product_type'],
        '#product_id' => $_POST['product_id'],
        'Tracking' => isset($_POST['Tracking']) ? $_POST['Tracking'] : "",

    );

    if (isset($_POST['delivery_lock']) || $_POST['delivery_date'] == "") {
        $data['#delivery_date'] = "NULL";
    } else {
        $data['delivery_date'] = $_POST['delivery_date'];
    }
    if ($dbc->Update("bs_orders_bwd", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['id'], "bwd-order-edit", $_POST['id'], array("orders" => $order));

        if (is_null($order['delivery_id'])) {

            if (!is_null($order['delivery_date'])) {

                $data = array(
                    "#id" => "DEFAULT",
                    "#type" => 1,
                    "delivery_date" => $order['delivery_date'],
                    "#created" => "NOW()",
                    "#updated" => "NOW()",
                    "#status" => 0,
                    "#amount" => $order['amount'],
                    "#user" => $os->auth['id'],
                    "comment" => ""
                );



                $dbc->Insert("bs_deliveries_bwd", $data);
                $delivery_id = $dbc->GetID();

                $code = "DB-" . sprintf("%08s", $delivery_id);
                $dbc->Update("bs_deliveries_bwd", array("code" => $code), "id=" . $delivery_id);
                $dbc->Update("bs_orders_bwd", array("delivery_id" => $delivery_id), "id=" . $order['id']);
            }
        } else {
            if (!is_null($order['delivery_date'])) {
                $delivery = $dbc->GetRecord("bs_deliveries_bwd", "*", "id=" . $order['delivery_id']);
                $dbc->Update("bs_deliveries_bwd", array(
                    "delivery_date" => $order['delivery_date'],
                    "#amount" => $order['amount'],
                    "#updated" => "NOW()"
                ), "id=" . $order['delivery_id']);
            } else {
                $dbc->Delete("bs_deliveries_bwd", "id=" . $order['delivery_id']);
                $dbc->Update("bs_orders_bwd", array("#delivery_id" => "NULL"), "id=" . $order['id']);
            }
        }
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
