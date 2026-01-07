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

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");


if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($_POST['customer_id'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุลูกค้า'
	));
} else if ($_POST['amount'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุจำนวน'
	));
} else if ($_POST['price'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดระบุราคา'
	));
} else if ($_POST['store'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please selected store'
	));
} else if ($_POST['orderable_type'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please selected Delivery Type'
	));
} else {
	$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $_POST['customer_id']);
	$total = $_POST['amount'] * $_POST['price'];
	if ($_POST['vat_type'] == "2") {
		$vat = $total * 0.07;
	} else {
		$vat = 0;
	}
	$net = $total + $vat;

	$data = array(
		'#id' => "DEFAULT",
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		"#customer_id" => $_POST['customer_id'],
		"#amount" => $_POST['amount'],
		"#price" => $_POST['price'],
		'#total' => $net,
		'#usd' => isset($_POST['price_usd']) ? $_POST['price_usd'] : 0,
		"store" => isset($_POST['store']) ? $_POST['store'] : "",
		"orderable_type" => isset($_POST['orderable_type']) ? $_POST['orderable_type'] : "",
		"#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : $rate_spot,
		"#rate_exchange" => $_POST['rate_exchange'] != "" ? $_POST['rate_exchange'] : $rate_spot,
		"remark" => $_POST['remark'],
		"#status" => 1,
		"#order_id" => "NULL",
		"#vat_type" => $_POST['vat_type'],
		"#sales" => isset($_POST['sales']) ? $_POST['sales'] : (is_null($customer['default_sales']) ? "NULL" : $customer['default_sales']),
		"#product_id" => $_POST['product_id']


	);

	if ($dbc->Insert("bs_quick_orders", $data)) {
		$quick_order_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $quick_order_id
		));

		$quick_order = $dbc->GetRecord("bs_quick_orders", "*", "id=" . $quick_order_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "quick_order-add", $quick_order_id, array("quick_orders" => $quick_order));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
