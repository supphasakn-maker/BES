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

$total_spot = 0;
$total_purchase = 0;

for ($i = 0; $i < count($_POST['spot_id']); $i++) {
	$total_spot += $_POST['spot_amount'][$i];
}

for ($i = 0; $i < count($_POST['purchase_id']); $i++) {
	$total_purchase += $_POST['purchase_amount'][$i];
}

if (count($_POST['spot_id']) < 1) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'There is no order item.'
	));
} else if ($_POST['date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input date.'
	));
} else if (count($_POST['purchase_id']) < 1) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'There is no purchase item.'
	));
} else if (round($total_spot, 2) != round($total_purchase, 2)) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'The number of order [' . $total_spot . '] is not equal purchase [' . $total_purchase . ']'
	));
} else {

	$data = array(
		"#id" => 'DEFAULT',
		"mapped" => $_POST['date'] . " " . date("H:i:s"),
		"#amount" => $total_spot,
		"remark" => $_POST['remark']
	);

	if ($dbc->Insert("bs_mapping_usd", $data)) {
		$mapping_id = $dbc->GetID();

		for ($i = 0; $i < count($_POST['spot_id']); $i++) {
			$purchase = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['spot_id'][$i]);
			$purchase_usd = $purchase['amount'] * ($purchase['rate_spot'] + $purchase['rate_pmdc']) * 32.1507;
			$data = array(
				"#id" => 'DEFAULT',
				"#mapping_id" => $mapping_id,
				"#purchase_id" => $_POST['spot_id'][$i],
				"#amount" => $_POST['spot_amount'][$i],
			);

			if ($_POST['spot_mapping_id'][$i] == "") {

				$dbc->Insert("bs_mapping_usd_spots", $data);

				if ($purchase_usd > $_POST['spot_amount'][$i]) {
					$data["#amount"] = $purchase_usd - $_POST['spot_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_usd_spots", $data);
				}
			} else {
				$mapping = $dbc->GetRecord("bs_mapping_usd_spots", "*", "id=" . $_POST['spot_mapping_id'][$i]);
				if ($mapping['amount'] > $_POST['spot_amount'][$i]) {
					$data["#amount"] = $mapping['amount'] - $_POST['spot_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_usd_spots", $data);
				}
				$dbc->Update("bs_mapping_usd_spots", array(
					"#amount" => $_POST['spot_amount'][$i],
					"#mapping_id" => $mapping_id
				), "id=" . $_POST['spot_mapping_id'][$i]);
			}
		}

		for ($i = 0; $i < count($_POST['purchase_id']); $i++) {
			$purchase = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $_POST['purchase_id'][$i]);

			$data = array(
				"#id" => 'DEFAULT',
				"#mapping_id" => $mapping_id,
				"#purchase_id" => $_POST['purchase_id'][$i],
				"#amount" => $_POST['purchase_amount'][$i],
			);

			if ($_POST['purchase_mapping_id'][$i] == "") {

				$dbc->Insert("bs_mapping_usd_purchases", $data);

				if ($purchase['amount'] > $_POST['purchase_amount'][$i]) {
					$data["#amount"] = $purchase['amount'] - $_POST['purchase_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_usd_purchases", $data);
				}
			} else {
				$mapping = $dbc->GetRecord("bs_mapping_usd_purchases", "*", "id=" . $_POST['purchase_mapping_id'][$i]);
				if ($mapping['amount'] > $_POST['purchase_amount'][$i]) {
					$data["#amount"] = $mapping['amount'] - $_POST['purchase_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_usd_purchases", $data);
				}
				$dbc->Update("bs_mapping_usd_purchases", array(
					"#amount" => $_POST['purchase_amount'][$i],
					"#mapping_id" => $mapping_id
				), "id=" . $_POST['purchase_mapping_id'][$i]);
			}
		}





		echo json_encode(array(
			'success' => true,
			'msg' => $mapping_id
		));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
