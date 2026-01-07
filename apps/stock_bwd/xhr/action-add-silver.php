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

$aCreated = array();
$aRedundant = array();

if ($_POST['product_id'] == "1") {
	$pack_name = "แท่ง 15 กรัม";
	$weight = "0.015";
	$pack_type = 'แท่ง';
} else if ($_POST['product_id'] == "2") {
	$pack_name = "แท่ง 50 กรัม";
	$weight = "0.050";
	$pack_type = 'แท่ง';
} else if ($_POST['product_id'] == "3") {
	$pack_name = "แท่ง 150 กรัม";
	$weight = "0.150";
	$pack_type = 'แท่ง';
} else if ($_POST['product_id'] == "5") {
	$pack_name = "กล่อง 15 กรัม";
	$weight = "0.150";
	$pack_type = 'กล่อง';
} else if ($_POST['product_id'] == "6") {
	$pack_name = "กล่อง 50 กรัม";
	$weight = "0.150";
	$pack_type = 'กล่อง';
} else if ($_POST['product_id'] == "7") {
	$pack_name = "กล่อง 150 กรัม";
	$weight = "0.150";
	$pack_type = 'กล่อง';
} else if ($_POST['product_id'] == "8") {
	$pack_name = "กล่องใหม่";
	$weight = "0.150";
	$pack_type = 'กล่อง';
}

if ($_POST['start'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input data!'
	));
} else if ($_POST['end'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input data!'
	));
} else if ($_POST['end'] > 1000) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'จำนวนต้องไม่เกิน 1000'
	));
} else if ($_POST['product_id'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input Product!'
	));
} else if ($_POST['product_type'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input Product Type!'
	));
} else if ($_POST['comment'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input comment!'
	));
} else {
	$counter = 0;
	for ($i = (int)$_POST['start']; $i < (int)$_POST['start'] + (int)$_POST['end']; $i++) {
		$code = $_POST['prefix'] . $i;
		if ($dbc->HasRecord("bs_stock_bwd", "code = '" . $code . "'")) {
			array_push($aRedundant, $code);
		} else {
			$data = array(
				"#id" => "DEFAULT",
				"code" => $code,
				"pack_name" => $pack_name,
				"pack_type" => $pack_type,
				"#weight_actual" => $weight,
				"#weight_expected" => $weight,
				"#amount" => 1,
				"#status" => 0,
				"#product_type" => $_POST['product_type'],
				'submited' => $_POST['date'],
				"#product_id" => $_POST['product_id'],
				"#created" => "NOW()",
				"comment" => $_POST['comment']
			);

			$dbc->Insert("bs_stock_bwd", $data);
			array_push($aCreated, $code);
		}


		$counter++;
	}

	echo json_encode(array(
		'success' => true,
		'msg' => $counter,
		'created' => $aCreated,
		'redundant' => $aRedundant
	));
}

$dbc->Close();
