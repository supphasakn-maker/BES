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

$amount = round($_POST['amount'], 4);
$total = round($_POST['split'] + $_POST['remain'], 4);

if (empty($_SESSION['auth']['user_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'คุณหมดเวลาการใช้งานแล้วโปรดรีเฟรชหน้าจอเพื่อเข้าสู่ระบบใหม่อีกครั้ง',
	));
	exit();
} else if ($amount != $total) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'The balance is not correct'
	));
} else {
	$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $_POST['id']);


	$data_split = array(
		'#id' => "DEFAULT",
		"bank" => $usd['bank'],
		"type" => $usd['type'],
		"#amount" => $_POST['split'],
		"#rate_exchange" => $usd['rate_exchange'],
		"#rate_finance" => $usd['rate_exchange'],
		"date" => $usd['date'],
		"comment" => $usd['comment'],
		"ref" => $usd['ref'],
		"#user" => $os->auth['id'],
		"#status" => $usd['status'],
		"confirm" => $usd['confirm'],
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		"#parent" => $usd['id'] // parent ชี้ไปที่ ID ดั้งเดิมที่ถูกแยก
	);


	$dbc->Insert("bs_purchase_usd", $data_split);

	$new_usd_id_split = $dbc->GetID();


	$data_profit_split = array(
		'#id' => "DEFAULT",
		"bank" => $usd['bank'],
		"type" => $usd['type'],
		"#amount" => $_POST['split'],
		"#rate_exchange" => $usd['rate_exchange'],
		"#rate_finance" => $usd['rate_exchange'],
		"date" => $usd['date'],
		"value_date" => $usd['date'],
		"comment" => $usd['comment'],
		"ref" => $usd['ref'],
		"#user" => $os->auth['id'],
		"#status" => $usd['status'],
		"confirm" => $usd['confirm'],
		'#created' => 'NOW()',
		'#updated' => 'NOW()',
		"#parent" => $usd['id'],
		"#purchase" => $new_usd_id_split
	);
	$dbc->Insert("bs_purchase_usd_profit", $data_profit_split);
	$dbc->Insert("bs_purchase_usd_profit_bwd", $data_profit_split);



	$data_remain = $data_split;
	$data_remain['#amount'] = $_POST['remain'];


	$dbc->Insert("bs_purchase_usd", $data_remain);

	$new_usd_id_remain = $dbc->GetID();


	$data_profit_remain = $data_profit_split;
	$data_profit_remain['#amount'] = $_POST['remain'];
	$data_profit_remain['#purchase'] = $new_usd_id_remain;
	$dbc->Insert("bs_purchase_usd_profit", $data_profit_remain);
	$dbc->Insert("bs_purchase_usd_profit_bwd", $data_profit_remain);



	$data_update_original = array(
		"#status" => -1,
		'#updated' => 'NOW()',
	);

	if ($dbc->Update("bs_purchase_usd", $data_update_original, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));

		$dbc->Update("bs_purchase_usd_profit", $data_update_original, "purchase=" . $_POST['id']);
		$dbc->Update("bs_purchase_usd_profit_bwd", $data_update_original, "purchase=" . $_POST['id']);

		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "usd-split", $_POST['id'], array("usds" => $usd));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
