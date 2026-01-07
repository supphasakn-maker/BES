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

function roundup($value, $decimals)
{
	$factor = pow(10, $decimals);
	return ceil($value * $factor) / $factor;
}

if ($_POST['amount'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input amount'
	));
} else if ($_POST['price'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input price'
	));
} else if ($_POST['sales'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please select sales'
	));
} else if ($_POST['store'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please selected store'
	));
} else {
	$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $_POST['customer_id']);
	$total = $_POST['amount'] * $_POST['price'];
	if ($_POST['vat_type'] == "2") {
		$vat = $total * 0.07;
	} else {
		$vat = 0;
	}
	$net = roundup($total, 4) + roundup($vat, 4);

	$data = array(
		"#customer_id" => $_POST['customer_id'],
		"customer_name" => $customer['name'],
		"date" => $_POST['date'],
		"#sales" => $_POST['sales'],
		'#type' => 1,
		'#updated' => 'NOW()',
		'#amount' => $_POST['amount'],
		'#price' => $_POST['price'],
		'#vat_type' => $_POST['vat_type'],
		'#vat' => $vat,
		'#total' => $total,
		'#net' => $net,
		'#usd' =>  $_POST['price_usd'],
		'delivery_time' => $_POST['delivery_time'],
		"comment" => $_POST['comment'],
		"shipping_address" => $_POST['shipping_address'],
		"billing_address" => $_POST['billing_address'],
		"#rate_spot" => $_POST['rate_spot'],
		"#rate_exchange" => $_POST['rate_exchange'],
		"currency" => $_POST['currency'],
		"info_payment" => $_POST['payment'],
		"info_contact" => $_POST['contact'],
		"#product_id" => $_POST['product_id'],
		"store" => $_POST['store'],
		"orderable_type" => $_POST['orderable_type']
	);

	if (isset($_POST['delivery_lock']) || $_POST['delivery_date'] == "") {
		$data['#delivery_date'] = "NULL";
	} else {
		$data['delivery_date'] = $_POST['delivery_date'];
	}

	if ($dbc->Update("bs_orders", $data, "id=" . $_POST['id'])) {

		$profit_record = $dbc->GetRecord("bs_orders_profit", "*", "order_id=" . $_POST['id']);

		if ($profit_record) {
			$profit_update_result = $dbc->Update("bs_orders_profit", $data, "order_id=" . $_POST['id']);
			if (!$profit_update_result) {
				error_log("Failed to update bs_orders_profit for order_id: " . $_POST['id']);
			}
		} else {
			$profit_data = $data;
			$profit_data['#id'] = "DEFAULT";
			$profit_data['#order_id'] = $_POST['id'];
			$profit_data['#created'] = 'NOW()';

			$original_order = $dbc->GetRecord("bs_orders", "code", "id=" . $_POST['id']);
			if ($original_order) {
				$profit_data['code'] = $original_order['code'];
			}

			$profit_insert_result = $dbc->Insert("bs_orders_profit", $profit_data);
			if (!$profit_insert_result) {
				error_log("Failed to insert bs_orders_profit for order_id: " . $_POST['id']);
			}
		}

		echo json_encode(array(
			'success' => true
		));

		$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['id'], "order-edit", $_POST['id'], array("orders" => $order));

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

				if ($customer['default_bank'] != "") {
					$json = array(
						"bank" => $customer['default_bank'],
						"payment" => $customer['default_payment'],
						"remark" => ''
					);
					$data['payment_note'] = json_encode($json, JSON_UNESCAPED_UNICODE);
				}

				$dbc->Insert("bs_deliveries", $data);
				$delivery_id = $dbc->GetID();

				$code = "D-" . sprintf("%08s", $delivery_id);
				$dbc->Update("bs_deliveries", array("code" => $code), "id=" . $delivery_id);
				$dbc->Update("bs_orders", array("delivery_id" => $delivery_id), "id=" . $order['id']);

				$dbc->Update("bs_orders_profit", array("delivery_id" => $delivery_id), "order_id=" . $order['id']);
			}
		} else {
			if (!is_null($order['delivery_date'])) {
				$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $order['delivery_id']);
				$dbc->Update("bs_deliveries", array(
					"delivery_date" => $order['delivery_date'],
					"#amount" => $order['amount'],
					"#updated" => "NOW()"
				), "id=" . $order['delivery_id']);

				$dbc->Update("bs_orders_profit", array(
					"delivery_date" => $order['delivery_date'],
					"#amount" => $order['amount'],
					"#updated" => "NOW()"
				), "order_id=" . $order['id']);
			} else {
				$dbc->Delete("bs_deliveries", "id=" . $order['delivery_id']);
				$dbc->Update("bs_orders", array("#delivery_id" => "NULL"), "id=" . $order['id']);

				$dbc->Update("bs_orders_profit", array("#delivery_id" => "NULL"), "order_id=" . $order['id']);
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
