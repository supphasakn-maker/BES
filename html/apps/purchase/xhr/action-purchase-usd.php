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

if ($_POST['date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input date?'
	));
} else {
	$data = array(
		'date' => $_POST['date'],
		'#confirm' => 'NOW()',
		'#status' => 1,
		'#updated' => 'NOW()'
	);

	if ($dbc->Update("bs_purchase_usd", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$dbc->Update("bs_purchase_usd_profit", $data, "purchase=" . $_POST['id']);
		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "usd-purchase", $_POST['id'], array("usds" => $usd));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
