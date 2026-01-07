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
} else if ($_POST['value_adjust_type'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Your value should not empty!'
	));
} else {
	$data = array(
		'#id' => "DEFAULT",
		"#supplier_id" => $_POST['supplier_id'],
		"date" => $_POST['date'],
		"#value_adjust_type	" => $_POST['value_adjust_type'],
		"#product_id" => $_POST['product_id']
	);

	if ($dbc->Insert("bs_adjust_defer", $data)) {
		$spot_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $spot_id
		));
		$spot = $dbc->GetRecord("bs_adjust_defer", "*", "id=" . $spot_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "defer-add", $spot_id, array("bs_adjust_defer" => $spot));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
