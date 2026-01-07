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

$user_id = intval($_SESSION['auth']['user_id']);
$user_data = $dbc->GetRecord("os_users", "*", "id=" . $user_id);

$can_delete = false;

if ($user_data && isset($user_data['gid'])) {
	$user_gid = intval($user_data['gid']);

	if (in_array($user_gid, array(1))) {
		$can_delete = true;
	}
}

if (!$can_delete) {
	header('Content-Type: application/json');
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณไม่มีสิทธิ์ในการลบ Order'
	));
	$dbc->Close();
	exit;
}

if (!isset($_POST['items']) || !is_array($_POST['items']) || count($_POST['items']) == 0) {
	header('Content-Type: application/json');
	echo json_encode(array(
		'success' => false,
		'msg' => 'ไม่พบรายการที่ต้องการลบ'
	));
	$dbc->Close();
	exit;
}

$deleted_count = 0;
$failed_items = array();

foreach ($_POST['items'] as $item) {
	$item = intval($item); 

	if ($dbc->HasRecord("bs_orders", "id=" . $item)) {
		$order = $dbc->GetRecord("bs_orders", "*", "id=" . $item);

		if (!is_null($order['parent'])) {
			$deletable = true;
			$aDelete = array();
			$sql = "SELECT * FROM bs_orders WHERE parent = " . intval($order['parent']) . " AND id != " . $item;
			$rst = $dbc->Query($sql);

			while ($line = $dbc->Fetch($rst)) {
				if ($dbc->HasRecord("bs_orders", "parent=" . intval($line['id']))) {
					$deletable = false;
				}
				array_push($aDelete, intval($line['id']));
			}

			if ($deletable) {
				foreach ($aDelete as $id) {
					$delete_result = $dbc->Delete("bs_orders", "id=" . intval($id));

					if ($delete_result) {
						$profit_delete_result = $dbc->Delete("bs_orders_profit", "order_id=" . intval($id));
						if (!$profit_delete_result) {
							error_log("Failed to delete from bs_orders_profit for order_id: " . $id);
						}

						$os->save_log(0, $user_id, "order-delete", $id, array("bs_orders" => $line));
					}
				}

				$delete_result = $dbc->Delete("bs_orders", "id=" . $item);

				if ($delete_result) {
					$profit_delete_result = $dbc->Delete("bs_orders_profit", "order_id=" . $item);
					if (!$profit_delete_result) {
						error_log("Failed to delete from bs_orders_profit for order_id: " . $item);
					}

					$dbc->Update("bs_orders", array('#status' => 1, '#updated' => 'NOW()'), "id=" . intval($order['parent']));

					$profit_update_result = $dbc->Update("bs_orders_profit", array('#status' => 1, '#updated' => 'NOW()'), "order_id=" . intval($order['parent']));
					if (!$profit_update_result) {
						error_log("Failed to update parent order status in bs_orders_profit for order_id: " . $order['parent']);
					}

					$os->save_log(0, $user_id, "order-delete", $item, array("bs_orders" => $order));

					$deleted_count++;
				} else {
					array_push($failed_items, $item);
					error_log("Failed to delete order: " . $item);
				}
			} else {
				array_push($failed_items, $item);
				error_log("Cannot delete order " . $item . " - has child orders");
			}
		} else {
			$dbc->Update("bs_quick_orders", array("#status" => 0), "order_id=" . $item);
			$delete_result = $dbc->Delete("bs_orders", "id=" . $item);

			if ($delete_result) {
				$profit_delete_result = $dbc->Delete("bs_orders_profit", "order_id=" . $item);
				if (!$profit_delete_result) {
					error_log("Failed to delete from bs_orders_profit for order_id: " . $item);
				}

				$os->save_log(0, $user_id, "order-delete", $item, array("bs_orders" => $order));

				$deleted_count++;
			} else {
				array_push($failed_items, $item);
				error_log("Failed to delete order: " . $item);
			}
		}
	} else {
		array_push($failed_items, $item);
		error_log("Order not found: " . $item);
	}
}

header('Content-Type: application/json');
if ($deleted_count > 0 && count($failed_items) == 0) {
	echo json_encode(array(
		'success' => true,
		'msg' => 'ลบ Order สำเร็จ (' . $deleted_count . ' รายการ)'
	));
} elseif ($deleted_count > 0 && count($failed_items) > 0) {
	echo json_encode(array(
		'success' => true,
		'msg' => 'ลบ Order บางส่วนสำเร็จ (' . $deleted_count . ' รายการ) แต่มี ' . count($failed_items) . ' รายการที่ลบไม่ได้',
		'failed_items' => $failed_items
	));
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => 'ไม่สามารถลบ Order ได้',
		'failed_items' => $failed_items
	));
}

$dbc->Close();
