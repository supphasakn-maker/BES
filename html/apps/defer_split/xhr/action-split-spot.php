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

if (count($_POST['split']) < 2) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'ต้องมากกว่า 2 ส่วนขึ้นไป'
	));
} else {
	$purchase = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);

	$data = array(
		"#id" => 'DEFAULT',
		"#purchase_id" => $purchase['id'],
		"#transfer_id" => 'NULL',
		"created" => $purchase['created'],
		"#updated" => 'NOW()',
		"remark" => ''
	);

	foreach ($_POST['split'] as $splited) {
		$data['#amount'] = $splited;
		$dbc->Insert("bs_spot_usd_splited", $data);
	}

	echo json_encode(array(
		'success' => true
	));

	$dbc->Update("bs_purchase_spot", array("#status" => -2), "id=" . $_POST['id']);
	$dbc->Update("bs_purchase_spot_profit", array("#status" => -2), "id=" . $_POST['id']);

	$import = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['id']);
	$os->save_log(0, $_SESSION['auth']['user_id'], "import-splited", $_POST['id'], array("bs_purchase_spot" => $purchase));
}

$dbc->Close();
