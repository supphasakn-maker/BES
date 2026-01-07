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
} else {

	$data = array(
		'name' => $_POST['name'],
		'#updated' => 'NOW()',
	);

	$data = array(
		"type" => $_POST['type'],
		'#updated' => 'NOW()',
		"date_claim" => $_POST['date_claim'],
		"#order_id" => $_POST['order_id'],
		"issue" => $_POST['issue'],
		"#amount" => $_POST['amount'] != "" ? $_POST['amount'] : "NULL",
		"pack_problem" => $_POST['pack_problem'],
		"pack_claim" => $_POST['pack_claim'],
		"detail" => $_POST['detail'],
		"org_name" => $_POST['org_name'],
		"contact_issuer" => $_POST['contact_issuer'],
		"contact_sender" => $_POST['contact_sender'],
		"contact_sales" => $_POST['contact_sales'],
		"solutions" => $_POST['solutions'] != "" ? $_POST['solutions'] : "",

	);

	if (isset($_POST['img_path'])) {
		$aImg = array();
		for ($i = 0; $i < count($_POST['img_path']); $i++) {
			array_push($aImg, array(
				"path" => $_POST['img_path'][$i],
				"desc" => $_POST['img_desc'][$i]
			));
		}
		$data["imgs"] = json_encode($aImg, JSON_UNESCAPED_UNICODE);
	}

	if ($dbc->Update("bs_claims", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$claim = $dbc->GetRecord("bs_claims", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "claim-edit", $_POST['id'], array("claim" => $claim));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}
$dbc->Close();
