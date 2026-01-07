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

$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_POST['id']);
$order = $dbc->GetRecord("bs_orders", "*", "delivery_id=" . $delivery['id']);

// Debug: แสดงค่าที่ได้รับ
error_log("POST data: " . print_r($_POST, true));
error_log("Delivery amount: " . $delivery['amount']);
error_log("Order amount: " . $order['amount']);

$total = 0;
for ($i = 0; $i < count($_POST['totaleach']); $i++) {
	// แปลงให้เป็น float และใช้ floatval เพื่อความแม่นยำ
	$total += floatval($_POST['totaleach'][$i]);
}

// Debug: แสดงยอดรวมที่คำนวณได้
error_log("Calculated total: " . $total);

// ใช้การเปรียบเทียบแบบ floating point ที่แม่นยำ
$delivery_amount = floatval($delivery['amount']);
$tolerance = 0.0001; // ความคลาดเคลื่อนที่ยอมรับได้

if (abs($total - $delivery_amount) > $tolerance) {
	echo json_encode(array(
		'success' => false,
		'msg' => "จำนวนกิโลไม่ตรงกับคำสั่งซื้อ (คำนวณได้: " . number_format($total, 4) . " ต้องการ: " . number_format($delivery_amount, 4) . ")",
		'debug' => array(
			'calculated' => $total,
			'required' => $delivery_amount,
			'difference' => abs($total - $delivery_amount)
		)
	));
} else {

	if ($dbc->HasRecord("bs_packings", "order_id=" . $order['id'])) {
		$packing = $dbc->GetRecord("bs_packings", "*", "order_id=" . $order['id']);
		$dbc->Delete("bs_packing_items", "packing_id=" . $packing['id']);
		$dbc->Delete("bs_packings", "id=" . $packing['id']);
	}

	$data = array(
		"#id" => "DEFAULT",
		"#type" => 1,
		"delivery" => $order['delivery_date'],
		"date" => date("Y-m-d"),
		"#created" => "NOW()",
		"#updated" => "NOW()",
		"#status" => 0,
		"#order_id" => $order['id'],
		"#parent" => "NULL",
		"#amount" => $delivery_amount, // ใช้ delivery amount แทน
		"#user" => $os->auth['id'],
		"comment" => ""
	);

	$dbc->Insert("bs_packings", $data);
	$packing_id = $dbc->GetID();

	for ($i = 0; $i < count($_POST['name']); $i++) {
		$data = array(
			"#id" => "DEFAULT",
			"name" => $_POST['name'][$i],
			"#size" => floatval($_POST['size'][$i]),
			"#amount" => floatval($_POST['amount'][$i]),
			"comment" => $_POST['comment'][$i],
			"packing_id" => $packing_id,
			"#status" => 0
		);
		$dbc->Insert("bs_packing_items", $data);
	}

	echo json_encode(array(
		'success' => true
	));
}

$dbc->Close();
