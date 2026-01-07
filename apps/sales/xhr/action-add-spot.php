<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

header('Content-Type: application/json');

try {
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	$selected_date_str = $_POST['date'];
	$selected_date = strtotime($selected_date_str);

	if (date("l", $selected_date) == "Thursday") {
		$date2 = date("Y-m-d", strtotime("+4 day", $selected_date));
	} else if (date("l", $selected_date) == "Friday") {
		$date2 = date("Y-m-d", strtotime("+4 day", $selected_date));
	} else {
		$date2 = date("Y-m-d", strtotime("+2 day", $selected_date));
	}

	if (empty($_SESSION['auth']['user_id'])) {
		echo json_encode(array(
			'success' => false,
			'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
		));
		exit();
	} else if (empty($_POST['amount'])) {
		echo json_encode(array(
			'success' => false,
			'msg' => 'Please input amount'
		));
		exit();
	} else {


		$delivery_id = null;
		$sellfix_id = null;
		$deli_id = null;

		$data = array(
			'#id' => "DEFAULT",
			"type" => $_POST['type'],
			"#supplier_id" => $_POST['supplier_id'],
			"#amount" => $_POST['amount'],
			"#rate_spot" => isset($_POST['rate_spot']) ? $_POST['rate_spot'] : 0,
			"#rate_pmdc" => isset($_POST['rate_pmdc']) ? $_POST['rate_pmdc'] : 0,
			"date" => $_POST['date'],
			"value_date" => isset($_POST['value_date']) ? $_POST['value_date'] : $_POST['date'],
			'#created' => 'NOW()',
			'#updated' => 'NOW()',
			"method" => isset($_POST['method']) ? $_POST['method'] : '',
			"maturity" => isset($_POST['maturity']) ? $_POST['maturity'] : '',
			"ref" => isset($_POST['ref']) ? $_POST['ref'] : '',
			"#user" => $os->auth['id'],
			"#status" => 1,
			"comment" => isset($_POST['comment']) ? addslashes($_POST['comment']) : "",
			"#trade_id" => 'NULL',
			"#product_id" => $_POST['product_id']
		);

		$datasmg = array(
			'#id' => "DEFAULT",
			"date" => $date2,
			"type" => $_POST['type'],
			"purchase_type" => 'Sell',
			"#amount" => $_POST['amount'],
			"#rate_spot" => isset($_POST['rate_spot']) ? $_POST['rate_spot'] : 0,
			"#rate_pmdc" => isset($_POST['rate_pmdc']) ? $_POST['rate_pmdc'] : 0
		);

		$datasellfix = array(
			'#id' => "DEFAULT",
			"#supplier_id" => $_POST['supplier_id'],
			"Type" => "Sell",
			"#amount" => $_POST['amount'],
			"#ounces" => $_POST['amount'] * 32.1507,
			"date" => $_POST['date'],
			"method" => isset($_POST['maturity']) ? $_POST['maturity'] : '',
			"#user" => $os->auth['id'],
			"#product_id" => $_POST['product_id'],
			"#status" => 1,
			"#created" => 'NOW()',
			"#updated" => 'NOW()',
			"#sales_spot" => 0
		);

		$datastx = array(
			'#id' => "DEFAULT",
			"date" => $date2,
			"type" => $_POST['type'],
			"purchase_type" => 'Sell',
			"#amount" => $_POST['amount'],
			"#rate_spot" => isset($_POST['rate_spot']) ? $_POST['rate_spot'] : 0,
			"#rate_pmdc" => isset($_POST['rate_pmdc']) ? $_POST['rate_pmdc'] : 0
		);

		if ($dbc->Insert("bs_sales_spot", $data)) {
			$spot_id = $dbc->GetID();
			error_log("bs_sales_spot inserted with ID: " . $spot_id);

			switch ($_POST['supplier_id']) {
				case "1":
					if ($dbc->Insert("bs_smg_trade", $datasmg)) {
						$delivery_id = $dbc->GetID();
					} else {
						error_log("Failed to insert bs_smg_trade");
					}
					break;

				case "6":
					if ($dbc->Insert("bs_smg_stx_trade", $datasmg)) {
						$deli_id = $dbc->GetID();
					} else {
						error_log("Failed to insert bs_smg_stx_trade");
					}
					break;
			}

			if (in_array($_POST['supplier_id'], ["1", "6"]) && $_POST['type'] == "Physical") {
				if ($dbc->Insert("bs_purchase_buyfix", $datasellfix)) {
					$sellfix_id = $dbc->GetID();
				} else {
					error_log("Failed to insert bs_purchase_buyfix");
					error_log("Last query executed on bs_purchase_buyfix table");
				}
			}


			$spot = $dbc->GetRecord("bs_sales_spot", "*", "id=" . $spot_id);

			if ($delivery_id) {
				$update_result = $dbc->Update("bs_smg_trade", array("purchase_spot" => $spot_id), "id=" . $delivery_id);
				error_log("Update bs_smg_trade result: " . ($update_result ? 'success' : 'failed'));
			}

			if ($deli_id) {
				$update_result = $dbc->Update("bs_smg_stx_trade", array("purchase_spot" => $spot_id), "id=" . $deli_id);
				error_log("Update bs_smg_trade result: " . ($update_result ? 'success' : 'failed'));
			}
			if ($sellfix_id) {
				$update_result = $dbc->Update("bs_purchase_buyfix", array("sales_spot" => $spot_id), "id=" . $sellfix_id);
				error_log("Update bs_purchase_buyfix result: " . ($update_result ? 'success' : 'failed'));
			}

			$os->save_log(0, $_SESSION['auth']['user_id'], "spot-add", $spot_id, array("bs_sales_spot" => $spot));

			echo json_encode(array(
				'success' => true,
				'msg' => 'เพิ่มข้อมูลสำเร็จ',
				'id' => $spot_id,
				'debug' => array(
					'delivery_id' => $delivery_id,
					'sellfix_id' => $sellfix_id,
					'supplier_id' => $_POST['supplier_id'],
					'type' => $_POST['type']
				)
			));
		} else {
			error_log("Failed to insert bs_sales_spot");
			echo json_encode(array(
				'success' => false,
				'msg' => "Insert Error: bs_sales_spot"
			));
		}
	}
} catch (Exception $e) {
	error_log("Exception: " . $e->getMessage());
	echo json_encode(array(
		'success' => false,
		'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
	));
} finally {
	if (isset($dbc)) {
		$dbc->Close();
	}
}
