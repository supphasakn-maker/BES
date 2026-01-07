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


if ($_POST['delivery_date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please Delivery Date'
	));
} else if ($_POST['select_spot'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please Select Spot'
	));
} else {


	$data = array(
		"#id" => "DEFAULT",
		"#supplier_id" => $_POST['supplier_id'],
		"#amount" => $_POST['amount'],
		"type" => $_POST['type'],
		"delivery_date" => $_POST['delivery_date'],
		"delivery_by" => $_POST['delivery_by'],
		"comment" => $_POST['comment'],
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		"#user" => $os->auth['id'],
		"#status" => 0
	);


	if ($dbc->Insert("bs_imports", $data)) {
		$import_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $import_id
		));

		foreach ($_POST['reserve'] as $reserve_id) {
			$dbc->Update("bs_reserve_silver", array(
				"#import_id" => $import_id
			), "id = " . $reserve_id);
		}

		$select_spot = explode(",", $_POST['select_spot']);
		$select_amount = explode(",", $_POST['select_amount']);

		for ($i = 0; $i < count($select_spot); $i++) {
			$purchase = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $select_spot[$i]);

			if ($purchase['amount'] == $select_amount[$i]) {
				$dbc->Update("bs_purchase_spot", array(
					"#status" => 2,
					"#import_id" => $import_id
				), "id = " . $select_spot[$i]);
				$dbc->Update("bs_purchase_spot_profit", array(
					"#status" => 2,
					"#import_id" => $import_id
				), "purchase = " . $select_spot[$i]);
			}
		}



		$import = $dbc->GetRecord("bs_imports", "*", "id=" . $import_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "import-add", $import_id, array("bs_imports" => $import));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
