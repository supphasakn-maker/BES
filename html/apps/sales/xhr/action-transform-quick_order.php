<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

// Add error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);

try {
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	// Validate required POST data
	if (!isset($_POST['id']) || empty($_POST['id'])) {
		throw new Exception("Missing order ID");
	}

	$order = $dbc->GetRecord("bs_quick_orders", "*", "id=" . (int)$_POST['id']);
	if (!$order) {
		throw new Exception("Quick order not found");
	}

	$customer = $dbc->GetRecord("bs_customers", "*", "id=" . (int)$order['customer_id']);
	if (!$customer) {
		throw new Exception("Customer not found");
	}

	$total = $order['amount'] * $order['price'];
	if ($order['vat_type'] == "2") {
		$vat = $total * 0.07;
	} else {
		$vat = 0;
	}
	$net = $total + $vat;
	$order_date = strtotime($order['created']);

	// Handle checkbox data properly
	$keep_silver = isset($_POST['keepsilver']) ? (int)$_POST['keepsilver'] : 0;
	$delivery_date = isset($_POST['delivery_date']) ? trim($_POST['delivery_date']) : '';
	$delivery_time = isset($_POST['delivery_time']) ? trim($_POST['delivery_time']) : 'none';

	// If keeping silver, clear delivery info
	if ($keep_silver == 1) {
		$delivery_date = '';
		$delivery_time = 'none';
	}

	$data = array(
		'#id' => "DEFAULT",
		"#customer_id" => $customer['id'],
		"customer_name" => $customer['name'] ?: '',
		"date" => $order['created'],
		"#sales" => is_null($customer['default_sales']) ? "NULL" : intval($customer['default_sales']),
		"#user" => intval($order['sales']),
		'#type' => 1,
		"#parent" => 'NULL',
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		'#amount' => floatval($order['amount']),
		'#price' => floatval($order['price']),
		'#vat_type' => intval($order['vat_type']),
		'#vat' => floatval($vat),
		'#total' => floatval($total),
		'#net' => floatval($net),
		'#usd' => floatval($order['usd']),
		"#status" => 1,
		"comment" => $order['remark'] ?: '',
		"shipping_address" => $customer['shipping_address'] ?: '',
		"billing_address" => $customer['billing_address'] ?: '',
		"#rate_spot" => floatval($order['rate_spot']),
		"#rate_exchange" => floatval($order['rate_exchange']),
		"currency" => "THB",
		'delivery_time' => $delivery_time ?: 'none',
		"info_payment" => $customer['default_payment'] ?: '',
		"info_contact" => $customer['contact'] ?: '',
		"#product_id" => intval($order['product_id']),
		"#keep_silver" => $keep_silver,
		"store" => $order['store'] ?: '',
		"orderable_type" => $order['orderable_type'] ?: '',
	);

	// Handle delivery date properly
	if ($keep_silver == 1 || empty($delivery_date)) {
		$data['#delivery_date'] = "NULL";
	} else {
		$data['delivery_date'] = $delivery_date;
	}

	// Debug: Log the data array before insert
	error_log("Data to insert: " . print_r($data, true));

	if ($dbc->Insert("bs_orders", $data)) {
		$order_id = $dbc->GetID();

		$code = "O-" . sprintf("%07s", $order_id);
		$dbc->Update("bs_orders", array("code" => $code), "id=" . $order_id);

		// เพิ่มข้อมูลเดียวกันไปที่ bs_orders_profit
		$profit_data = $data; // คัดลอกข้อมูลเดิม
		$profit_data['#order_id'] = $order_id; // เพิ่ม order_id
		$profit_data['code'] = $code; // เพิ่ม code

		// Insert ข้อมูลไปที่ bs_orders_profit
		if ($dbc->Insert("bs_orders_profit", $profit_data)) {
			// Insert สำเร็จ
			$profit_id = $dbc->GetID();
		} else {
			// หากเกิดข้อผิดพลาดในการ Insert bs_orders_profit
			error_log("Failed to insert into bs_orders_profit for order_id: " . $order_id);
		}

		// Update quick order status
		if (!$dbc->Update("bs_quick_orders", array(
			"#status" => 2,
			"#order_id" => $order_id
		), "id=" . (int)$_POST['id'])) {
			error_log("Failed to update quick order status for id: " . $_POST['id']);
		}

		// Get the created order for logging
		$order_record = $dbc->GetRecord("bs_orders", "*", "id=" . $order_id);
		if ($order_record) {
			$os->save_log(0, $os->auth['id'], "order-add-byquickorder", $order_id, array("order" => $order_record));
		}

		// Handle delivery creation if not keeping silver and has delivery date
		if ($keep_silver == 0 && !empty($delivery_date)) {
			$delivery_data = array(
				"#id" => "DEFAULT",
				"#type" => 1,
				"delivery_date" => $delivery_date,
				"#created" => "NOW()",
				"#updated" => "NOW()",
				"#status" => 0,
				"#amount" => $order['amount'],
				"#user" => $os->auth['id'],
				"comment" => ""
			);

			if (!empty($customer['default_bank'])) {
				$json = array(
					"bank" => $customer['default_bank'],
					"payment" => $customer['default_payment'],
					"remark" => ''
				);
				$delivery_data['payment_note'] = json_encode($json, JSON_UNESCAPED_UNICODE);
			}

			if ($dbc->Insert("bs_deliveries", $delivery_data)) {
				$delivery_id = $dbc->GetID();

				$delivery_code = "D-" . sprintf("%07s", $delivery_id);
				$dbc->Update("bs_deliveries", array("code" => $delivery_code), "id=" . $delivery_id);
				$dbc->Update("bs_orders", array("delivery_id" => $delivery_id), "id=" . $order_id);

				// อัปเดต delivery_id ใน bs_orders_profit ด้วย
				$dbc->Update("bs_orders_profit", array("delivery_id" => $delivery_id), "order_id=" . $order_id);
			} else {
				// Don't fail the whole process for delivery creation failure
				error_log("Warning: Failed to create delivery for order_id: " . $order_id);
			}
		}

		// Return success response
		header('Content-Type: application/json');
		echo json_encode(array(
			'success' => true,
			'msg' => "คำสั่งซื้อ " . $code,
			'order_id' => $order_id
		));
	} else {
		throw new Exception("Failed to insert order");
	}
} catch (Exception $e) {
	// Log the error
	// error_log("Transform quick order error: " . $e->getMessage());

	// Return error response
	header('Content-Type: application/json');
	echo json_encode(array(
		'success' => false,
		'msg' => "เกิดข้อผิดพลาด: " . $e->getMessage()
	));
} finally {
	if (isset($dbc)) {
		$dbc->Close();
	}
}
