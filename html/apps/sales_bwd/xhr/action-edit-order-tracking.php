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


$data = array(
    'Tracking' => isset($_POST['Tracking']) ? $_POST['Tracking'] : "",

);

if ($dbc->Update("bs_orders_bwd", $data, "id=" . $_POST['id'])) {
    echo json_encode(array(
        'success' => true
    ));
    $order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);
    $os->save_log(0, $_SESSION['auth']['id'], "bwd-order-edit", $_POST['id'], array("orders" => $order));
} else {
    echo json_encode(array(
        'success' => false,
        'msg' => "No Change"
    ));
}


$dbc->Close();
