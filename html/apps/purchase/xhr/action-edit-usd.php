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

if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($_POST['amount'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Pleser input amount'
	));
} else {
	$data = array(
		"bank" => $_POST['bank'],
		"type" => $_POST['type'],
		"#amount" => $_POST['amount'],
		"#rate_exchange" => $_POST['rate_exchange'],
		"date" => $_POST['date'],
		"comment" => $_POST['comment'],
		"method" => $_POST['method'],
		"ref" => $_POST['ref'],
		'#updated' => 'NOW()'
	);
	$data2 = array(
		"bank" => $_POST['bank'],
		"type" => $_POST['type'],
		"#amount" => $_POST['amount'],
		"#rate_exchange" => $_POST['rate_exchange'],
		"date" => $_POST['date'],
		"value_date" => isset($_POST['value_date']) ? $_POST['value_date'] : $_POST['date'],
		"comment" => $_POST['comment'],
		"method" => $_POST['method'],
		"ref" => $_POST['ref'],
		'#updated' => 'NOW()'
	);

	if ($dbc->Update("bs_purchase_usd", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$dbc->Update("bs_purchase_usd_profit", $data2, "purchase=" . $_POST['id']);
		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "usd-edit", $_POST['id'], array("usd" => $usd));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
