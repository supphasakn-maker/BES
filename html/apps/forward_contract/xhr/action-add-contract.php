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
} else if ($_POST['date'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Please input date'
	));
} else {
	$data = array(
		"#id" => "DEFAULT",
		"bank" => $_POST['bank'],
		"date" => $_POST['date'],
		"type" => $_POST['type'],
		"#supplier_id" => $_POST['supplier_id'],
		"#value_usd_goods" => $_POST['value_usd_goods'],
		"#value_usd_deposit" => $_POST['value_usd_deposit'],
		"#value_usd_paid" => $_POST['value_usd_paid'],
		"#value_usd_adjusted" => $_POST['value_usd_adjusted'],
		"#value_usd_total" => $_POST['value_usd_total'],
		"#value_usd_fixed" => $_POST['value_usd_fixed'],
		"#value_usd_nonfixed" => $_POST['value_usd_nonfixed'],
		"#rate_counter" => $_POST['rate_counter'],
		"#value_thb_fixed" => $_POST['value_thb_fixed'],
		"#value_thb_premium" => $_POST['value_thb_premium'],
		"#value_thb_net" => $_POST['value_thb_net'],
		"#created" => 'NOW()',
		"#updated" => 'NOW()',
		"remark" => '',
		"#value_thb_transaction" => $_POST['value_thb_transaction'],
		"#paid_thb" => 0,
		"#paid_usd" => 0,
		"#status" => 1,
		"#source" => $_POST['source']
	);

	if ($dbc->Insert("bs_transfers", $data)) {
		$transfer_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $transfer_id
		));

		if (isset($_POST['ajusted_name'])) {
			for ($i = 0; $i < count($_POST['ajusted_name']); $i++) {
				$data = array(
					"#id" => "DEFAULT",
					"#transfer_id" => $transfer_id,
					"name" => $_POST['ajusted_name'][$i],
					"#value" => $_POST['ajusted_value'][$i],
				);
				$dbc->Insert("bs_transfer_adjusted", $data);
			}
		}

		if (isset($_POST['purchase']))
			if ($_POST['source'] == "1") {
				for ($i = 0; $i < count($_POST['purchase']); $i++) {
					$purchase_id = $_POST['purchase'][$i];
					$dbc->Update("bs_purchase_spot", array(
						"#transfer_id" => $transfer_id
					), "id = " . $purchase_id);
					$dbc->Update("bs_purchase_spot_profit", array(
						"#transfer_id" => $transfer_id
					), "purchase = " . $purchase_id);
				}
			} else if ($_POST['source'] == "2") {
				for ($i = 0; $i < count($_POST['purchase']); $i++) {
					$purchase_id = $_POST['purchase'][$i];
					$dbc->Update("bs_imports", array(
						"#transfer_id" => $transfer_id
					), "id = " . $purchase_id);
				}
			} else if ($_POST['source'] == "3") {
				for ($i = 0; $i < count($_POST['purchase']); $i++) {
					$purchase_id = $_POST['purchase'][$i];
					$dbc->Update("bs_import_usd_splited", array(
						"#transfer_id" => $transfer_id
					), "id = " . $purchase_id);
				}
			}

		if (isset($_POST['usd_id']))
			for ($i = 0; $i < count($_POST['usd_id']); $i++) {

				$data = array(
					"#purchase_id" => $_POST['usd_id'][$i],
					"#transfer_id" => $transfer_id,
					"#premium_type" => 1,
					"date" => $_POST['date'],
					"#premium_day" => $_POST['usd_premium_day'][$i] != "" ? $_POST['usd_premium_day'][$i] : 0,
					"#rate_premium" => $_POST['usd_rate_premium'][$i] != "" ? $_POST['usd_rate_premium'][$i] : 0,
					"#rate_counter" => $_POST['rate_counter'],
					"#premium" => $_POST['usd_premium'][$i],
					"fw_contract_no" => $_POST['usd_fw_contact_no'][$i]
				);

				if ($_POST['usd_date_premium_start'][$i] == "") {
					$data['#premium_start'] = "NULL";
				} else {
					$data['premium_start'] = $_POST['usd_date_premium_start'][$i];
				}
				if ($_POST['usd_date_premium_end'][$i] == "") {
					$data['#premium_end'] = "NULL";
				} else {
					$data['premium_end'] = $_POST['usd_date_premium_end'][$i];
				}

				$dbc->Insert("bs_transfer_usd ", $data);
			}

		$contract = $dbc->GetRecord("bs_transfers", "*", "id=" . $transfer_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "contract-add", $transfer_id, array("contracts" => $contract));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
