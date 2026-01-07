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

if ($_POST['customer_id'] == "") {
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
} else {
	$total = $_POST['amount'] * $_POST['price'];
	if ($_POST['vat_type'] == "2") {
		$vat = $total * 0.07;
	} else {
		$vat = 0;
	}
	$net = $total + $vat;

	$data = array(
		'#updated' => 'NOW()',
		"#customer_id" => $_POST['customer_id'],
		"#amount" => $_POST['amount'],
		"#price" => $_POST['price'],
		"#usd" => $_POST['price_usd'],
		"#total" => $net,
		"#rate_spot" => $_POST['rate_spot'],
		"#rate_exchange" => $_POST['rate_exchange'],
		"remark" => $_POST['remark'],
		"#vat_type" => $_POST['vat_type'],
		"#product_id" => $_POST['product_id']
	);

	if ($dbc->Update("bs_quick_orders", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$quick_order = $dbc->GetRecord("bs_quick_orders", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "quick_order-edit", $_POST['id'], array("quick_orders" => $quick_order));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
