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

if ($_POST['delivery_date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input new delivery date?'
	));
} else if ($_POST['delivery_date'] == $order['delivery_date']) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'วันที่ต้องไม่เหมือนกัน'
	));
} else {

	$data = array(
		"#id" => "default",
		"#order_id" => $_POST['id'],
		"delivery_date_old" => $order['delivery_date'],
		"delivery_date_new" => $_POST['delivery_date'],
		"reason_customer" => $_POST['reason_customer'],
		"reason_company" => $_POST['reason_company'],
		"#created" => "NOW()"
	);
	$dbc->Insert("bs_order_postpone", $data);

	$data = array(
		"delivery_date" => $_POST['delivery_date'],
		'#updated' => 'NOW()'
	);

	$data1 = array(
		"delivery_date" => $_POST['delivery_date'],
		"#keep_silver" => 0,
		'#updated' => 'NOW()'
	);

	if ($dbc->Update("bs_orders", $data1, "id=" . $_POST['id'])) {

		$profit_update_result = $dbc->Update("bs_orders_profit", $data1, "order_id=" . $_POST['id']);
		if (!$profit_update_result) {
			error_log("Failed to update delivery_date in bs_orders_profit for order_id: " . $_POST['id']);
		}

		echo json_encode(array(
			'success' => true
		));

		$dbc->Update("bs_deliveries", $data, "id=" . $order['delivery_id']);

		$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "order-postpone", $_POST['id'], array("bs_orders" => $order));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
