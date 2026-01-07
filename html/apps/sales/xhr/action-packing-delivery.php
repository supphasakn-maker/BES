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



$total = 0;
$debug_items = array();

for ($i = 0; $i < count($_POST['totaleach']); $i++) {
	$item_value = floatval($_POST['totaleach'][$i]);
	$total += $item_value;

	$debug_items[] = array(
		'index' => $i,
		'original' => $_POST['totaleach'][$i],
		'converted' => $item_value
	);
}

$delivery_amount = floatval($delivery['amount']);
$difference = abs($total - $delivery_amount);

error_log("Calculated total: " . $total);
error_log("Difference: " . $difference);

// ใช้ tolerance สำหรับการเปรียบเทียบ floating point
$tolerance = 0.0001;

if ($difference > $tolerance) {
	echo json_encode(array(
		'success' => false,
		'msg' => "จำนวนกิโลไม่ตรงกับคำสั่งซื้อ (คำนวณได้: " . number_format($total, 4) . " ต้องการ: " . number_format($delivery_amount, 4) . ")",
		'debug' => array(
			'calculated_total' => $total,
			'delivery_amount' => $delivery_amount,
			'difference' => $difference,
			'items' => $debug_items
		)
	));
} else {

	// ลบข้อมูลเดิม
	$dbc->Delete("bs_delivery_items", "delivery_id=" . $delivery['id']);

	// เพิ่มข้อมูลใหม่
	for ($i = 0; $i < count($_POST['name']); $i++) {
		$data = array(
			"#id" => "DEFAULT",
			"delivery_id" => $delivery['id'],
			"name" => $_POST['name'][$i],
			"#size" => floatval($_POST['size'][$i]), // แปลงเป็น float
			"#amount" => floatval($_POST['amount'][$i]), // แปลงเป็น float
			"comment" => $_POST['comment'][$i],
			"#status" => 0
		);
		$dbc->Insert("bs_delivery_items", $data);
	}

	// อัพเดทสถานะ delivery
	$dbc->Update("bs_deliveries", array("#status" => 1), "id=" . $delivery['id']);

	echo json_encode(array(
		'success' => true
	));
}

$dbc->Close();
