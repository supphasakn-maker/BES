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
		'msg' => 'Your Amount should not empty!'
	));
} else {
	$data = array(
		'#id' => "DEFAULT",
		"#supplier_id" => $_POST['supplier_id'],
		"date" => $_POST['date'],
		"#amount" => $_POST['amount'],
		"#usd" => $_POST['usd']
	);

	if ($dbc->Insert("bs_adjust_purchase", $data)) {
		$spot_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $spot_id
		));
		$spot = $dbc->GetRecord("bs_adjust_purchase", "*", "id=" . $spot_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "purchase-add", $spot_id, array("bs_adjust_purchase" => $spot));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
