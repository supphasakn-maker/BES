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
} else if ($_POST['phone'] == "" || $_POST['phone'] == 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input Phone!!'
    ));
} else if ($_POST['amount'] == "" || $_POST['amount'] == 0) {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input bars'
    ));
} else if ($_POST['vat_type'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please input Vats'
    ));
} else if ($_POST['price'] == "") {
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

    if ($_POST['vat_type'] == "7") {
        $vat = $total * 0.07;
    } else {
        $vat = 0;
    }
    $net = $total + $vat;

    $data = array(
        '#id' => "DEFAULT",
        "customer_name" => $_POST['customer_name'],
        "phone" => $_POST['phone'],
        "platform" => $_POST['platform'],
        "date" => $_POST['date'],
        "#sales" => $os->auth['id'],
        "#user" => $os->auth['id'],
        '#type' => 1,
        "#parent" => 'NULL',
        '#created' => 'NOW()',
        '#updated' => 'NOW()',
        '#amount' => $_POST['amount'],
        '#price' => $_POST['price'],
        '#vat_type' => $_POST['vat_type'],
        '#vat' => $vat,
        '#total' => $total,
        '#net' => $net,
        "#status" => 1,
        'comment' => isset($_POST['comment']) ? $_POST['comment'] : "",
        "engrave" =>  $_POST['engrave'],
        '#product_type' => $_POST['product_type'],
        '#product_id' => $_POST['product_id'],

    );



    if ($dbc->Insert("bs_orders_back_bwd", $data)) {
        $order_id = $dbc->GetID();
        $code = "BD-" . sprintf("%07s", $order_id);
        $dbc->Update("bs_orders_back_bwd", array("code" => $code), "id=" . $order_id);

        echo json_encode(array(
            'success' => true,
            'msg' => "Your Order ID " . $code . " was created!",
            'order_id' => $order_id
        ));

        $order = $dbc->GetRecord("bs_orders_back_bwd", "*", "id=" . $order_id);
        $os->save_log(0, $_SESSION['auth']['user_id'], "bwd-orderback-add", $order_id, array("bwd-ordersback" => $order));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "Insert Error"
        ));
    }
}

$dbc->Close();
