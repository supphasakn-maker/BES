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

foreach ($_POST['items'] as $item) {
	$contract = $dbc->GetRecord("bs_transfers", "*", "id=" . $item);
	$dbc->Delete("bs_transfers", "id=" . $item);
	$dbc->Delete("bs_transfer_payments", "transfer_id=" . $item);
	$dbc->Delete("bs_transfer_usd", "transfer_id=" . $item);

	$data = array(
		"#premium" => "NULL",
		"#transfer_id" => "NULL",
		"fw_contract_no" => "",
		"#bank_date" => "NULL",
		"#premium_start" => "NULL"
	);
	$dbc->Update("bs_purchase_usd", $data, "transfer_id = " . $item);
	$dbc->Update("bs_purchase_usd_profit", $data, "transfer_id = " . $item);
	$dbc->Update("bs_purchase_spot", array("#transfer_id" => "NULL"), "transfer_id = " . $item);
	$dbc->Update("bs_purchase_spot_profit", array("#transfer_id" => "NULL"), "transfer_id = " . $item);
	$dbc->Update("bs_imports", array("#transfer_id" => "NULL"), "transfer_id = " . $item);

	$os->save_log(0, $_SESSION['auth']['user_id'], "contract-delete", $id, array("contracts" => $contract));
}

$dbc->Close();
