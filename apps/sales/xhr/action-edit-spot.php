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

if ($_POST['amount'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input amount'
	));
} else {
	$data = array(
		"type" => $_POST['type'],
		"#supplier_id" => $_POST['supplier_id'],
		"#amount" => $_POST['amount'],
		"#rate_spot" => $_POST['rate_spot'],
		"#rate_pmdc" => $_POST['rate_pmdc'],
		"date" => $_POST['date'],
		"value_date" => $_POST['value_date'],
		'#updated' => 'NOW()',
		"method" => $_POST['method'],
		"maturity" => $_POST['maturity'],
		"ref" => $_POST['ref'],
		"comment" => $_POST['comment'],
		"#product_id" => $_POST['product_id']
	);
	$datasmg = array(
		"date" => $date2,
		"type" => $_POST['type'],
		"purchase_type"  => 'Sell',
		"#amount" => $_POST['amount'],
		"#rate_spot" => $_POST['rate_spot'] != "" ? $_POST['rate_spot'] : "NULL"
	);

	$datasellfix = array(
		"#supplier_id" => $_POST['supplier_id'],
		"#amount" => $_POST['amount'],
		"#ounces" => $_POST['amount'] * 32.1507,
		"date" => $_POST['date'],
		"method" => isset($_POST['maturity']) ? $_POST['maturity'] : '',
		"#user" => $os->auth['id'],
		"#product_id" => $_POST['product_id'],
		"#status" => 1,
		"#created" => 'NOW()',
		"#updated" => 'NOW()',
		"#sales_spot" => 0
	);

	if ($dbc->Update("bs_sales_spot", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));

		if ($_POST['supplier_id'] == "1" || $_POST['type'] == "physical") {
			$dbc->Update("bs_smg_trade", $datasmg, "purchase_spot=" . $_POST['id']);
		} else if ($_POST['supplier_id'] == "6" || $_POST['type'] == "physical") {
			$dbc->Update("bs_smg_stx_trade", $datasmg, "purchase_spot=" . $_POST['id']);
		} else {
		}
		if ($_POST['supplier_id'] == "1" && $_POST['type'] == "physical") {
			$dbc->Update("bs_purchase_buyfix", $datasellfix, "sales_spot=" . $_POST['id']);
		} else {
		}
		$spot = $dbc->GetRecord("bs_sales_spot", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "spot-edit", $_POST['id'], array("bs_sales_spot" => $spot));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
