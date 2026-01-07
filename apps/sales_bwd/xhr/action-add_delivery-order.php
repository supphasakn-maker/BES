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

$order = $dbc->GetRecord("bs_orders_bwd", "*", "id=" . $_POST['id']);
if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($_POST['delivery_date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => "Please input delivery date"
	));
} else {

	$data = array(
		"#id" => "DEFAULT",
		"#type" => 1,
		"delivery_date" => $_POST['delivery_date'],
		"#created" => "NOW()",
		"#updated" => "NOW()",
		"#status" => 0,
		"#amount" => $order['amount'],
		"#user" => $os->auth['id'],
		"comment" => ""
	);



	if ($dbc->Insert("bs_deliveries_bwd", $data)) {
		$delivery_id = $dbc->GetID();
		$code = "DB-" . sprintf("%07s", $delivery_id);
		$dbc->Update("bs_deliveries_bwd", array("code" => $code), "id=" . $delivery_id);
		echo json_encode(array(
			'success' => true,
			'code' => $code
		));
		$dbc->Update("bs_orders_bwd", array("delivery_id" => $delivery_id, "delivery_date" => $_POST['delivery_date']), "id=" . $order['id']);
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}


$dbc->Close();
