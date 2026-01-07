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

$total_order = 0;
$total_purchase = 0;

for ($i = 0; $i < count($_POST['order_id']); $i++) {
	$total_order += $_POST['order_amount'][$i];
}

for ($i = 0; $i < count($_POST['purchase_id']); $i++) {
	$total_purchase += $_POST['purchase_amount'][$i];
}

if (count($_POST['order_id']) < 1) {
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
} else if (round($total_order, 4) != round($total_purchase, 4)) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'The number of order [' . $total_order . '] is not equal purchase [' . $total_purchase . ']'
	));
} else {

	$data = array(
		"#id" => 'DEFAULT',
		"mapped" => $_POST['date'] . " " . date("H:i:s"),
		"#amount" => $total_order,
		"remark" => $_POST['remark']
	);

	if ($dbc->Insert("bs_mapping_silvers", $data)) {
		$mapping_id = $dbc->GetID();

		// Loop for ORDER
		for ($i = 0; $i < count($_POST['order_id']); $i++) {
			$order = $dbc->GetRecord("bs_orders", "*", "id=" . $_POST['order_id'][$i]);
			$data = array(
				"#id" => 'DEFAULT',
				"#mapping_id" => $mapping_id,
				"#order_id" => $_POST['order_id'][$i],
				"#amount" => $_POST['order_amount'][$i],
			);

			// Case : No Matching Before
			if ($_POST['order_mapping_id'][$i] == "") {
				$dbc->Insert("bs_mapping_silver_orders", $data);
				if ($order['amount'] > $_POST['order_amount'][$i]) {
					$data["#amount"] = $order['amount'] - $_POST['order_amount'][$i];
					$data["#mapping_id"] = "NULL";

					$dbc->Insert("bs_mapping_silver_orders", $data);
				}
				// Case : Already Match Some
			} else {
				$mapping = $dbc->GetRecord("bs_mapping_silver_orders", "*", "id=" . $_POST['order_mapping_id'][$i]);
				if ($mapping['amount'] > $_POST['order_amount'][$i]) {
					$data["#amount"] = $mapping['amount'] - $_POST['order_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_silver_orders", $data);
				}

				$dbc->Update("bs_mapping_silver_orders", array(
					"#amount" => $_POST['order_amount'][$i],
					"#mapping_id" => $mapping_id
				), "id=" . $_POST['order_mapping_id'][$i]);
			}
		}

		// Loop for Purchase
		for ($i = 0; $i < count($_POST['purchase_id']); $i++) {
			$purchase = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['purchase_id'][$i]);

			$data = array(
				"#id" => 'DEFAULT',
				"#mapping_id" => $mapping_id,
				"#purchase_id" => $_POST['purchase_id'][$i],
				"#amount" => $_POST['purchase_amount'][$i],
			);

			if ($_POST['purchase_mapping_id'][$i] == "") {
				$dbc->Insert("bs_mapping_silver_purchases", $data);
				$mapping_item_id = $dbc->GetID();

				//For USD Automatic
				$data_usd = $data;
				$data_usd["#mapping_id"] = "NULL";
				$data_usd["#amount"] = $data['#amount'] * ($purchase['rate_spot'] + $purchase['rate_pmdc']) * 32.1507;
				$data_usd["#silver_item_id"] = $mapping_item_id;

				if ($purchase['currency'] != "THB")
					$dbc->Insert("bs_mapping_usd_spots", $data_usd);


				if ($purchase['amount'] > $_POST['purchase_amount'][$i]) {
					$data["#amount"] = $purchase['amount'] - $_POST['purchase_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_silver_purchases", $data);
					$mapping_item_id = $dbc->GetID();

					$data_usd = $data;
					$data_usd["#amount"] = $data['#amount'] * ($purchase['rate_spot'] + $purchase['rate_pmdc']) * 32.1507;
					$data_usd["#silver_item_id"] = $mapping_item_id;

					if ($purchase['currency'] != "THB")
						$dbc->Insert("bs_mapping_usd_spots", $data_usd);
				}
			} else {
				$mapping = $dbc->GetRecord("bs_mapping_silver_purchases", "*", "id=" . $_POST['purchase_mapping_id'][$i]);
				if ($mapping['amount'] > $_POST['purchase_amount'][$i]) {
					$data["#amount"] = $mapping['amount'] - $_POST['purchase_amount'][$i];
					$data["#mapping_id"] = "NULL";
					$dbc->Insert("bs_mapping_silver_purchases", $data);
					$mapping_item_id = $dbc->GetID();

					$data_usd = $data;
					$data_usd["#amount"] = $data['#amount'] * ($purchase['rate_spot'] + $purchase['rate_pmdc']) * 32.1507;
					$data_usd["#silver_item_id"] = $mapping_item_id;
					if ($purchase['currency'] != "THB")
						$dbc->Insert("bs_mapping_usd_spots", $data_usd);
				}

				$dbc->Update("bs_mapping_silver_purchases", array(
					"#amount" => $_POST['purchase_amount'][$i],
					"#mapping_id" => $mapping_id
				), "id=" . $_POST['purchase_mapping_id'][$i]);

				if ($dbc->HasRecord("bs_mapping_usd_spots", "silver_item_id=" . $_POST['purchase_mapping_id'][$i])) {
					$spot_usd_mapping = $dbc->GetRecord("bs_mapping_usd_spots", "id", "silver_item_id=" . $_POST['purchase_mapping_id'][$i]);

					$dbc->Update("bs_mapping_usd_spots", array(
						"#amount" => $_POST['purchase_amount'][$i] * ($purchase['rate_spot'] + $purchase['rate_pmdc']) * 32.1507
					), "id=" . $spot_usd_mapping['id']);
				}
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
