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

$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['id']);
$dbc->Delete("bs_deliveries", "id=" . $order['delivery_id']);

$data = array(
	"#delivery_date" => "NULL",
	"#keep_silver" => 1,
	"#delivery_id" => "NULL"
);

if ($dbc->Update("bs_orders", $data, "id=" . $_POST['id'])) {

	$profit_update_result = $dbc->Update("bs_orders_profit", $data, "order_id=" . $_POST['id']);
	if (!$profit_update_result) {
		error_log("Failed to update delivery_id to NULL in bs_orders_profit for order_id: " . $_POST['id']);
	}

	echo json_encode(array(
		'success' => true
	));

	$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['id']);
	$os->save_log(0, $_SESSION['auth']['user_id'], "order-lock", $_POST['id'], array("orders" => $order));
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => "No Change"
	));
}

$dbc->Close();
