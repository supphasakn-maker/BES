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

if (isset($_POST['item'])) {
	// ลบ delivery เดียว
	$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_POST['item']);

	$dbc->Update("bs_orders", array("#delivery_id" => "NULL"), "delivery_id=" . $_POST['item']);

	// อัปเดต delivery_id เป็น NULL ใน bs_orders_profit ด้วย
	$affected_orders = $dbc->GetRecord("bs_orders", "id", "delivery_id=" . $_POST['item']);
	foreach ($affected_orders as $order) {
		$profit_update_result = $dbc->Update("bs_orders_profit", array("#delivery_id" => "NULL"), "order_id=" . $order['id']);
		if (!$profit_update_result) {
			error_log("Failed to update delivery_id to NULL in bs_orders_profit for order_id: " . $order['id']);
		}
	}

	$dbc->Delete("bs_delivery_items", "delivery_id=" . $_POST['item']);
	$dbc->Delete("bs_deliveries", "id=" . $_POST['item']);
	$os->save_log(0, $_SESSION['auth']['user_id'], "delivery-delete", $_POST['item'], array("delivery" => $delivery));
} else {
	// ลบหลาย delivery
	foreach ($_POST['items'] as $item) {
		$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $item);

		$dbc->Update("bs_orders", array("#delivery_id" => "NULL"), "delivery_id=" . $item);

		// อัปเดต delivery_id เป็น NULL ใน bs_orders_profit ด้วย
		$affected_orders = $dbc->GetRecord("bs_orders", "id", "delivery_id=" . $item);
		foreach ($affected_orders as $order) {
			$profit_update_result = $dbc->Update("bs_orders_profit", array("#delivery_id" => "NULL"), "order_id=" . $order['id']);
			if (!$profit_update_result) {
				error_log("Failed to update delivery_id to NULL in bs_orders_profit for order_id: " . $order['id']);
			}
		}

		$dbc->Delete("bs_delivery_items", "delivery_id=" . $item);
		$dbc->Delete("bs_deliveries", "id=" . $item);
		$os->save_log(0, $_SESSION['auth']['user_id'], "delivery-delete", $item, array("delivery" => $delivery));
	}
}

$dbc->Close();
