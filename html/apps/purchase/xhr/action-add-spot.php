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


function sendResponse($success, $message, $data = null)
{
	$response = array('success' => $success, 'msg' => $message);
	if ($data !== null) {
		$response['data'] = $data;
	}
	echo json_encode($response);
	exit();
}


function calculateValueDate($selectedDate)
{
	$timestamp = strtotime($selectedDate);
	$dayOfWeek = date("l", $timestamp);

	if (in_array($dayOfWeek, ["Thursday", "Friday"])) {
		return date("Y-m-d", strtotime("+4 day", $timestamp));
	}
	return date("Y-m-d", strtotime("+2 day", $timestamp));
}


if (empty($_SESSION['auth']['user_id'])) {
	sendResponse(false, 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง');
}


if (empty($_POST['amount']) || !is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
	sendResponse(false, 'กรุณาระบุจำนวนกิโลที่ถูกต้อง');
}

if (empty($_POST['supplier_id']) || !is_numeric($_POST['supplier_id'])) {
	sendResponse(false, 'กรุณาเลือก Supplier');
}


if (empty($_POST['date'])) {
	sendResponse(false, 'กรุณาระบุวันที่');
}

if (empty($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
	sendResponse(false, 'กรุณาเลือก Product');
}


$selected_date_str = $_POST['date'];
$date2 = calculateValueDate($selected_date_str);


$data = array(
	'#id' => "DEFAULT",
	"#supplier_id" => $_POST['supplier_id'],
	"type" => $_POST['type'],
	"noted" => isset($_POST['noted']) ? $_POST['noted'] : '',
	"#amount" => $_POST['amount'],
	"#rate_spot" => !empty($_POST['rate_spot']) ? $_POST['rate_spot'] : "NULL",
	"#rate_pmdc" => !empty($_POST['rate_pmdc']) ? $_POST['rate_pmdc'] : "NULL",
	"#THBValue" => !empty($_POST['THBValue']) ? $_POST['THBValue'] : "NULL",
	"currency" => isset($_POST['currency']) ? $_POST['currency'] : '',
	"date" => $_POST['date'],
	"value_date" => isset($_POST['value_date']) ? $_POST['value_date'] : $_POST['date'],
	'#created' => 'NOW()',
	'#updated' => 'NOW()',
	"method" => isset($_POST['method']) ? $_POST['method'] : '',
	"ref" => isset($_POST['ref']) ? $_POST['ref'] : '',
	"#user" => $os->auth['id'],
	"#status" => isset($_POST['pending']) ? 0 : 1,
	"#confirm" => isset($_POST['pending']) ? 'NULL' : 'NOW()',
	"#order_id" => 'NULL',
	"comment" => isset($_POST['comment']) ? $_POST['comment'] : "",
	"#adj_supplier" => isset($_POST['adj_supplier']) ? $_POST['adj_supplier'] : 0,
	"#product_id" => $_POST['product_id']
);


$datasmg = array(
	'#id' => "DEFAULT",
	"date" => $date2,
	"type" => $_POST['type'],
	"purchase_type" => 'Buy',
	"#amount" => $_POST['amount'],
	"#rate_spot" => !empty($_POST['rate_spot']) ? $_POST['rate_spot'] : "NULL",
	"#rate_pmdc" => 0
);




$datasellfix = array(
	'#id' => "DEFAULT",
	"supplier_id" => $_POST['supplier_id'],
	"type" => "Buy",
	"amount" => $_POST['amount'],
	"ounces" => $_POST['amount'] * 32.1507,
	"date" => $_POST['date'],
	"method" => "Today",
	"img" => "",
	"user" => $os->auth['id'],
	"product_id" => $_POST['product_id'],
	"status" => 1,
	'#created' => 'NOW()',
	'#updated' => 'NOW()',
	"purchase_spot" => 0
);

$datasales = array(
	'#id' => "DEFAULT",
	"type" => 'Physical',
	"#supplier_id" => 1,
	"#amount" => $_POST['amount'],
	"#rate_spot" => 0,
	"#rate_pmdc" => 0,
	"date" => $_POST['date'],
	"value_date" => isset($_POST['value_date']) ? $_POST['value_date'] : $_POST['date'],
	'#created' => 'NOW()',
	'#updated' => 'NOW()',
	"method" => 'Via Message',
	"maturity" => 'Today',
	"ref" => isset($_POST['ref']) ? $_POST['ref'] : '',
	"#user" => $os->auth['id'],
	"#status" => 1,
	"comment" => isset($_POST['comment']) ? addslashes($_POST['comment']) : "",
	"#trade_id" => 'NULL',
	"#product_id" => $_POST['product_id']
);

$databuyfix = array(
	'#id' => "DEFAULT",
	"#supplier_id" => 1,
	"Type" => "Sell",
	"#amount" => $_POST['amount'],
	"#ounces" => $_POST['amount'] * 32.1507,
	"date" => $_POST['date'],
	"method" => 'Today',
	"#user" => $os->auth['id'],
	"#product_id" => $_POST['product_id'],
	"#status" => 1,
	"#created" => 'NOW()',
	"#updated" => 'NOW()',
	"#sales_spot" => 0
);

$datasmgsales = array(
	'#id' => "DEFAULT",
	"date" => $date2,
	"type" => $_POST['type'],
	"purchase_type" => 'Sell',
	"#amount" => $_POST['amount'],
	"#rate_spot" => isset($_POST['rate_spot']) ? $_POST['rate_spot'] : 0,
	"#rate_pmdc" => 0
);

$delivery_id = null;
$sellfix_id = null;
$spot_id = null;
$bb = null;
$sales_spot_id = null;
$buyfix_id = null;
$smg_sales_id = null;

try {




	if ($dbc->Insert("bs_purchase_spot", $data)) {
		$spot_id = $dbc->GetID();


		if ($dbc->Insert("bs_purchase_spot_profit", $data)) {
			$bb = $dbc->GetID();
		} else {
			error_log("Failed to insert bs_purchase_spot_profit");
		}


		if ($_POST['supplier_id'] == "1" && in_array($_POST['type'], ["physical", "trade"])) {
			if ($dbc->Insert("bs_smg_trade", $datasmg)) {
				$delivery_id = $dbc->GetID();
			} else {
				error_log("Failed to insert bs_smg_trade");
			}
		}


		if ($_POST['supplier_id'] == "6" && $_POST['type'] == "physical") {
			if (!$dbc->Insert("bs_smg_stx_trade", $datasmg)) {
				$delivery_id = $dbc->GetID();
				error_log("Failed to insert bs_smg_stx_trade");
			}
		}


		$allowed_suppliers = ["17", "18", "22", "23","24"];
		if (in_array($_POST['supplier_id'], $allowed_suppliers) && $_POST['type'] == "physical-adjust") {
			if ($dbc->Insert("bs_purchase_buy", $datasellfix)) {
				$sellfix_id = $dbc->GetID();
				error_log("Success: bs_purchase_buy inserted with ID: " . $sellfix_id);

				if ($dbc->Insert("bs_sales_spot", $datasales)) {
					$sales_spot_id = $dbc->GetID();
					error_log("Success: bs_sales_spot inserted with ID: " . $sales_spot_id);

					if ($dbc->Insert("bs_purchase_buyfix", $databuyfix)) {
						$buyfix_id = $dbc->GetID();
						error_log("Success: bs_purchase_buyfix inserted with ID: " . $buyfix_id);

						$dbc->Update("bs_purchase_buyfix", array("sales_spot" => $sales_spot_id), "id=" . $buyfix_id);
						error_log("Success: bs_purchase_buyfix updated with sales_spot ID: " . $sales_spot_id);
					} else {
						error_log("Failed to insert bs_purchase_buyfix");
					}

					if ($dbc->Insert("bs_smg_trade", $datasmgsales)) {
						$smg_sales_id = $dbc->GetID();
						error_log("Success: bs_smg_trade (sales) inserted with ID: " . $smg_sales_id);

						$dbc->Update("bs_smg_trade", array("purchase_spot" => $sales_spot_id), "id=" . $smg_sales_id);
						error_log("Success: bs_smg_trade updated with purchase_spot (sales_spot) ID: " . $sales_spot_id);
					} else {
						error_log("Failed to insert bs_smg_trade (sales)");
					}
				} else {
					error_log("Failed to insert bs_sales_spot");
				}
			} else {
				error_log("Failed to insert bs_purchase_buy");
				error_log("Data attempted: " . print_r($datasellfix, true));
			}
		}

		$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $spot_id);


		if ($bb !== null) {
			$dbc->Update("bs_purchase_spot_profit", array("purchase" => $spot_id), "id=" . $bb);
		}


		if ($delivery_id !== null) {
			$dbc->Update("bs_smg_trade", array("purchase_spot" => $spot_id), "id=" . $delivery_id);
		}


		if ($delivery_id !== null) {
			$dbc->Update("bs_smg_stx_trade", array("purchase_spot" => $spot_id), "id=" . $delivery_id);
		}


		if ($sellfix_id !== null) {
			$dbc->Update("bs_purchase_buy", array("purchase_spot" => $spot_id), "id=" . $sellfix_id);
		}


		$os->save_log(0, $_SESSION['auth']['user_id'], "spot-add", $spot_id, array("spot" => $spot));



		sendResponse(true, $spot_id, array(
			'spot_id' => $spot_id,
			'delivery_id' => $delivery_id,
			'sellfix_id' => $sellfix_id,
			'sales_spot_id' => $sales_spot_id,
			'buyfix_id' => $buyfix_id,
			'smg_sales_id' => $smg_sales_id
		));
	} else {


		error_log("Failed to insert bs_purchase_spot");
		sendResponse(false, "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง");
	}
} catch (Exception $e) {


	error_log("Purchase spot error: " . $e->getMessage());
	sendResponse(false, "เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง");
}

$dbc->Close();
