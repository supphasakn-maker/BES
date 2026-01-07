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

$selected_date_str = $_POST['date']; // เก็บค่าวันที่ที่ส่งมา
$selected_date = strtotime($selected_date_str); // แปลงเป็น timestamp

if (date("l", $selected_date) == "Thursday") {
	$date2 = date("Y-m-d", strtotime("+4 day", $selected_date));
} else if (date("l", $selected_date) == "Friday") {
	$date2 = date("Y-m-d", strtotime("+4 day", $selected_date));
} else {
	$date2 = date("Y-m-d", strtotime("+2 day", $selected_date));
}


if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($_POST['amount'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Your Amount should not empty!'
	));
} else {

	$data = array(
		"#supplier_id" => $_POST['supplier_id'],
		"type" => $_POST['type'],
		"#amount" => $_POST['amount'],
		"#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : "NULL",
		"#rate_pmdc" => $_POST['rate_pmdc'] != "" ? $_POST['rate_pmdc'] : "NULL",
		"#THBValue" => $_POST['THBValue'] != "" ? $_POST['THBValue'] : "NULL",
		"currency" => $_POST['currency'],
		"date" => $_POST['date'],
		"value_date" => $_POST['value_date'],
		'#updated' => 'NOW()',
		"method" => $_POST['method'],
		"ref" => $_POST['ref'],
		"comment" => $_POST['comment'],
		"#adj_supplier" => $_POST['adj_supplier'],
		"#product_id" => $_POST['product_id'],
		"noted" => $_POST['noted']
	);

	$datasmg = array(
		"date" => $date2,
		"type" => $_POST['type'],
		"purchase_type"  => 'Buy',
		"#amount" => $_POST['amount'],
		"#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : "NULL",
		"#rate_pmdc" => 0
	);

	$datasellfix = array(
		"#supplier_id" => $_POST['supplier_id'],
		"Type" => "Buy",
		"#amount" => $_POST['amount'],
		"#ounces" => $_POST['amount'] * 32.1507,
		"date" => $_POST['date'],
		"method" => "Today",
		"#user" => $os->auth['id'],
		"#product_id" => $_POST['product_id'],
		"#status" => 1,
		"#created" => 'NOW()',
		"#updated" => 'NOW()',
	);

	if ($dbc->Update("bs_purchase_spot", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$dbc->Update("bs_purchase_spot_profit", $data, "purchase=" . $_POST['id']);

		if ($_POST['supplier_id'] == "1" || $_POST['type'] == "physical") {
			$dbc->Update("bs_smg_trade", $datasmg, "purchase_spot=" . $_POST['id']);
		}

		if ($_POST['supplier_id'] == "6" || $_POST['type'] == "physical") {
			$dbc->Update("bs_smg_stx_trade", $datasmg, "purchase_spot=" . $_POST['id']);
		}

		$allowed_suppliers = ["17", "18", "22", "23"];
		if (in_array($_POST['supplier_id'], $allowed_suppliers) && $_POST['type'] == "physical-adjust") {
			if ($dbc->Update("bs_purchase_buy", $datasellfix, "purchase_spot=" . $_POST['id'])) {
			} else {
				error_log("Failed to Edit bs_purchase_buy");
				error_log("Last query executed on bs_purchase_buy table");
			}
		}
		$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "spot-edit", $_POST['id'], array("spot" => $spot));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
