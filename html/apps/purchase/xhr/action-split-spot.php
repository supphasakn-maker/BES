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

$amount = round($_POST['amount'], 4);
$total = round($_POST['split'] + $_POST['remain'], 4);
/*
	var_dump($amount);
	var_dump($total);
	var_dump(strval($amount) == strval($total));
	*/


if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($amount != $total) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'The balance is not correct [' . floatval($_POST['amount']) . '][' . floatval($_POST['split'] + $_POST['remain']) . ']'
	));
} else {
	$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);

	$data = array(
		'#id' => "DEFAULT",
		"#supplier_id" => $spot['supplier_id'],
		"type" => $spot['type'],
		"#amount" => $_POST['split'],
		"#rate_spot" => $spot['rate_spot'],
		"#rate_pmdc" => $spot['rate_pmdc'],
		"date" => $spot['date'],
		"value_date" => $spot['value_date'],
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		"method" => $spot['method'],
		"ref" => $spot['ref'],
		"#user" => $os->auth['id'],
		"#status" => $spot['status'],
		"confirm" => $spot['confirm'],
		"#order_id" => 'NULL',
		"comment" => $spot['comment'],
		"#parent" => $_POST['id']
	);
	$dbc->Insert("bs_purchase_spot", $data);
	$dbc->Insert("bs_purchase_spot_profit", $data);
	$data['#amount'] = $_POST['remain'];
	$dbc->Insert("bs_purchase_spot", $data);
	$dbc->Insert("bs_purchase_spot_profit", $data);

	$data = array(
		"#status" => -1,
		'#updated' => 'NOW()',
	);

	if ($dbc->Update("bs_purchase_spot", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$dbc->Update("bs_purchase_spot_profit", $data, "purchase=" . $_POST['id']);
		$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "spot-split", $_POST['id'], array("spots" => $spot));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
