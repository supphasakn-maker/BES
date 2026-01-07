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

if ($_POST['phone'] == "" || $_POST['phone'] == 0) {
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
} else if ($_POST['product'] == "") {
    echo json_encode(array(
        'success' => false,
        'msg' => 'Please Select Product!!'
    ));
} else if ($_POST['type'] == "") {
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
        "customer_name" => $_POST['customer_name'],
        "phone" => $_POST['phone'],
        "platform" => $_POST['platform'],
        "#sales" => $os->auth['id'],
        "#user" => $os->auth['id'],
        '#type' => 1,
        '#updated' => 'NOW()',
        '#amount' => $_POST['amount'],
        '#price' => $_POST['price'],
        '#vat_type' => $_POST['vat_type'],
        '#vat' => $vat,
        '#total' => $total,
        '#net' => $net,
        "engrave" =>  $_POST['engrave'],
        '#product_type' => $_POST['type'],
        '#product_id' => $_POST['product'],

    );

    if ($dbc->Update("bs_orders_back_bwd", $data, "id=" . $_POST['id'])) {
        echo json_encode(array(
            'success' => true
        ));
        $order = $dbc->GetRecord("bs_orders_back_bwd", "*", "id=" . $_POST['id']);
        $os->save_log(0, $_SESSION['auth']['id'], "bs_orders_back_bwd-edit", $_POST['id'], array("orders" => $order));
    } else {
        echo json_encode(array(
            'success' => false,
            'msg' => "No Change"
        ));
    }
}

$dbc->Close();
