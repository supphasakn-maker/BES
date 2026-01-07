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

$order_id = intval($_POST['id']);
if ($order_id <= 0) {
	echo json_encode(['success' => false, 'msg' => 'Invalid order ID']);
	$dbc->Close();
	exit;
}

if (!isset($_POST['amount']) || !is_array($_POST['amount']) || count($_POST['amount']) < 2) {
	echo json_encode(['success' => false, 'msg' => 'กรุณาระบุจำนวนอย่างน้อย 2 รายการ']);
	$dbc->Close();
	exit;
}

if (!isset($_POST['date']) || !is_array($_POST['date']) || count($_POST['date']) != count($_POST['amount'])) {
	echo json_encode(['success' => false, 'msg' => 'ข้อมูลวันที่ไม่ครบถ้วน']);
	$dbc->Close();
	exit;
}

$check_profit_orders = $dbc->GetRecord("bs_mapping_profit_orders", "*", "order_id=" . $order_id);
$check_profit_orders_usd = $dbc->GetRecord("bs_mapping_profit_orders_usd", "*", "order_id=" . $order_id);

if ($check_profit_orders || $check_profit_orders_usd) {
	echo json_encode([
		'success' => false,
		'msg' => "ไม่สามารถดำเนินการได้ เนื่องจากมีข้อมูลใน Profit Mapping แล้ว ก่อน Split แจ้ง Trader ด้วยค่ะ",
		'warning' => true
	]);
	$dbc->Close();
	exit;
}

$order = $dbc->GetRecord("bs_orders", "*", "id=" . $order_id);
if (!$order) {
	echo json_encode(['success' => false, 'msg' => 'ไม่พบข้อมูล Order']);
	$dbc->Close();
	exit;
}

$total_amount = 0;
$amounts = [];
$dates = [];

for ($i = 0; $i < count($_POST['amount']); $i++) {
	$amount = floatval($_POST['amount'][$i]);

	if ($amount <= 0) {
		echo json_encode(['success' => false, 'msg' => 'จำนวนต้องมากกว่า 0 ทุกรายการ (รายการที่ ' . ($i + 1) . ')']);
		$dbc->Close();
		exit;
	}

	$date = trim($_POST['date'][$i]);
	if ($date != "" && !strtotime($date)) {
		echo json_encode(['success' => false, 'msg' => 'รูปแบบวันที่ไม่ถูกต้อง (รายการที่ ' . ($i + 1) . ')']);
		$dbc->Close();
		exit;
	}

	$amounts[] = $amount;
	$dates[] = $date;
	$total_amount += $amount;
}

$original_amount = floatval($order['amount']);
if (abs($total_amount - $original_amount) > 0.01) {
	echo json_encode([
		'success' => false,
		'msg' => "ผลรวมจำนวน ({$total_amount}) ไม่ตรงกับต้นฉบับ ({$original_amount})"
	]);
	$dbc->Close();
	exit;
}

$customer = $dbc->GetRecord("bs_customers", "*", "id=" . intval($order['customer_id']));

$dbc->Query("START TRANSACTION");

$success = true;
$error_msg = "";
$split_count = 0;

try {
	for ($i = 0; $i < count($amounts); $i++) {
		$amount = $amounts[$i];
		$date = $dates[$i];

		$total = $amount * floatval($order['price']);

		if (trim($order['vat_type']) == "2") {
			$vat = round($amount * floatval($order['price']) * 0.07, 2);
		} else {
			$vat = 0;
		}

		$net = $total + $vat;

		$data = array(
			'#id' => "DEFAULT",
			"code" => $order['code'] . "." . ($i + 1),
			"#customer_id" => intval($order['customer_id']),
			"customer_name" => $order['customer_name'],
			"date" => $order['date'],
			"#sales" => !empty($order['sales']) ? intval($order['sales']) : "NULL",
			"#user" => intval($order['user']),
			"#type" => intval($order['type']),
			"#parent" => $order_id,
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#amount" => $amount,
			"#price" => floatval($order['price']),
			"#vat_type" => $order['vat_type'],
			"#vat" => $vat,
			"#total" => $total,
			"#net" => $net,
			"delivery_time" => $order['delivery_time'],
			"#status" => 1,
			"#keep_silver" => 0,
			"comment" => $order['comment'],
			"shipping_address" => $order['shipping_address'],
			"billing_address" => $order['billing_address'],
			"#rate_spot" => floatval($order['rate_spot']),
			"#rate_exchange" => floatval($order['rate_exchange']),
			"billing_id" => $order['billing_id'],
			"currency" => $order['currency'],
			"info_payment" => $order['info_payment'],
			"info_contact" => $order['info_contact'],
			"#product_id" => intval($order['product_id']),
			"store" => $order['store'],
			"orderable_type" => $order['orderable_type']
		);

		if ($date == "") {
			$data['#delivery_date'] = "NULL";
		} else {
			$data['delivery_date'] = $date;
		}

		if (!$dbc->Insert("bs_orders", $data)) {
			$success = false;
			$error_msg = "ไม่สามารถสร้าง Split Order รายการที่ " . ($i + 1) . " ได้";
			break;
		}

		$new_order_id = $dbc->GetID();
		$order_a = $dbc->GetRecord("bs_orders", "*", "id=" . $new_order_id);

		if (!is_null($order_a['delivery_date']) && $order_a['delivery_date'] != '') {
			$delivery_data = array(
				"#id" => "DEFAULT",
				"#type" => 1,
				"delivery_date" => $order_a['delivery_date'],
				"#created" => "NOW()",
				"#updated" => "NOW()",
				"#status" => 0,
				"#amount" => $order_a['amount'],
				"#user" => intval($os->auth['id']),
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

			if (!$dbc->Insert("bs_deliveries", $delivery_data)) {
				$success = false;
				$error_msg = "ไม่สามารถสร้าง Delivery รายการที่ " . ($i + 1) . " ได้";
				break;
			}

			$delivery_id = $dbc->GetID();
			$code = "D-" . sprintf("%07s", $delivery_id);

			if (!$dbc->Update("bs_deliveries", array("code" => $code), "id=" . $delivery_id)) {
				$success = false;
				$error_msg = "ไม่สามารถอัพเดท Delivery Code ได้";
				break;
			}

			if (!$dbc->Update("bs_orders", array("#delivery_id" => $delivery_id), "id=" . $new_order_id)) {
				$success = false;
				$error_msg = "ไม่สามารถเชื่อม Delivery กับ Order ได้";
				break;
			}
		}

		$split_count++;
	}

	if ($success) {
		$update_data = array(
			'#status' => 0,
			'#updated' => 'NOW()'
		);

		if (!$dbc->Update("bs_orders", $update_data, "id=" . $order_id)) {
			$success = false;
			$error_msg = "ไม่สามารถอัพเดท Order ต้นฉบับได้";
		}
	}

	if ($success) {
		$dbc->Query("COMMIT");

		$updated_order = $dbc->GetRecord("bs_orders", "*", "id=" . $order_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "order-split", $order_id, array("bs_orders" => $updated_order));

		echo json_encode([
			'success' => true,
			'msg' => "แยก Order เป็น {$split_count} รายการสำเร็จ"
		]);
	} else {
		$dbc->Query("ROLLBACK");
		echo json_encode([
			'success' => false,
			'msg' => $error_msg
		]);
	}
} catch (Exception $e) {
	$dbc->Query("ROLLBACK");
	echo json_encode([
		'success' => false,
		'msg' => "เกิดข้อผิดพลาด: " . $e->getMessage()
	]);
}

$dbc->Close();
