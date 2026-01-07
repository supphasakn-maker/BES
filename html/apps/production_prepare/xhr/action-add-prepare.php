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



if ($dbc->HasRecord("bs_productions", "round = '" . $_POST['import_lot'] . "'")) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Prepare Round is already exist.'
	));
} else if ($_POST['balance'] < 0) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'จำนวนต้องไม่ติดลบ'
	));
} else {
	$data = array(
		'#id' => "DEFAULT",
		'round' => $_POST['import_lot'],
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		'#user' => $os->auth['id'],
		"supplier" => $_POST['supplier'],
		'#status' => 0,
		"#weight_in_safe" => 0,
		"#weight_in_plate" => 0,
		"#weight_in_nugget" => 0,
		"#weight_in_blacknugget" => 0,
		"#weight_in_whitedust" => 0,
		"#weight_in_blackdust" => 0,
		"#weight_in_refine" => 0,
		"#weight_in_1" => $_POST['amount'],
		"#weight_in_2" => 0,
		"#weight_in_3" => 0,
		"#weight_in_4" => 0,
		"#weight_in_total" => 0,
		"#weight_out_safe" => 0,
		"#weight_out_plate" => 0,
		"#weight_out_nugget" => 0,
		"#weight_out_blacknugget" => 0,
		"#weight_out_whitedust" => 0,
		"#weight_out_blackdust" => 0,
		"#weight_out_refine" => 0,
		"#weight_out_packing" => 0,
		"#weight_out_total" => 0,
		"#weight_margin" => 0,
		"#product_id" => $_POST['product_type_id'],
		"#round_summary" => 0,
		"PMR" => $_POST['PMR'],
	);



	if ($dbc->Insert("bs_productions", $data)) {
		$prepare_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $prepare_id
		));

		if ($_POST['balance'] == 0) {
			$databalance = array(
				'#amount_balance' => $_POST['balance'],
				'#status' => -2,
			);
			$dbc->Update("bs_productions_round", $databalance, "id=" . $_POST['round_id']);
		} else if ($_POST['balance'] > 0) {
			$databalance = array(
				'#amount_balance' => $_POST['balance']

			);
			$dbc->Update("bs_productions_round", $databalance, "id=" . $_POST['round_id']);
		}

		$prepare = $dbc->GetRecord("bs_productions", "*", "id=" . $prepare_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "prepare-add", $prepare_id, array("prepares" => $prepare));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
