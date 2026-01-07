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


if ($_POST['weight_out_total'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดใส่น้ำหนักจริง'
	));
} else {

	$data = array(
		'#id' => "DEFAULT",
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		'#user' => $_SESSION['auth']['user_id'],
		'remark' => $_POST['remark'],
		'#weight_out_packing' => $_POST['weight_out_packing'],
		'#weight_out_total' => $_POST['weight_out_total'],
		'#status' => 0,
		'#product_id' => $_POST['product_id'],
	);

	if ($dbc->Insert("bs_productions_pmr", $data)) {
		$pmr_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $pmr_id
		));

		$round = "BES-" . sprintf("%07s", $pmr_id);
		$dbc->Update("bs_productions_pmr", array("round" => $round), "id=" . $pmr_id);

		$pmr = $dbc->GetRecord("bs_productions_pmr", "*", "id=" . $pmr_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "bs_productions_pmr-add", $pmr_id, array("bs_productions_pmr" => $pmr));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
