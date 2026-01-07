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
		'msg' => 'Please select date!'
	));
} else if (!isset($_POST['sales'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please select sales!'
	));
} else if (!isset($_POST['purchase'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please select purchase!'
	));
} else {
	$sales = array();
	$purchase = array();

	$total_sales = 0;
	$total_purchase = 0;
	foreach ($_POST['sales'] as $sales_id) {
		$sales_item = $dbc->GetRecord("bs_sales_spot", "*", "id=" . $sales_id);
		array_push($sales, $sales_item);
		$total_sales += $sales_item['amount'];
	}

	foreach ($_POST['purchase'] as $purchase_id) {
		$purchase_item = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $purchase_id);
		array_push($purchase, $purchase_item);
		$total_purchase += $purchase_item['amount'];
	}

	if ($total_sales != $total_purchase) {
		echo json_encode(array(
			'success' => false,
			'msg' => "Amount is not match!"
		));
	} else {
		$data = array(
			'#id' => "DEFAULT",
			"date" => $_POST['date'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"#user" => $os->auth['id']
		);

		if ($dbc->Insert("bs_trade_spot", $data)) {
			$trading_id = $dbc->GetID();
			foreach ($_POST['sales'] as $sales_id) {
				$dbc->Update("bs_sales_spot", array(
					"#status" => 2,
					"#trade_id" => $trading_id
				), "id=" . $sales_id);
			}
			foreach ($_POST['purchase'] as $purchase_id) {
				$dbc->Update("bs_purchase_spot", array(
					"#status" => 2,
					"#trade_id" => $trading_id
				), "id=" . $purchase_id);

				$dbc->Update("bs_purchase_spot_profit", array(
					"#status" => 2,
					"#trade_id" => $trading_id
				), "purchase=" . $purchase_id);
			}

			echo json_encode(array(
				'success' => true,
				'msg' => $trading_id
			));

			$trading = $dbc->GetRecord("bs_trade_spot", "*", "id=" . $trading_id);
			$os->save_log(0, $_SESSION['auth']['user_id'], "trading-add", $trading_id, array("tradings" => $trading));
		} else {
			echo json_encode(array(
				'success' => false,
				'msg' => "Insert Error"
			));
		}
	}
}

$dbc->Close();
