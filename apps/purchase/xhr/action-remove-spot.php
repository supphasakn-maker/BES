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

if (isset($_POST['item'])) {

	$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $_POST['item']);
	$dbc->Delete("bs_purchase_spot", "id=" . $_POST['item']);
	$dbc->Delete("bs_purchase_spot_profit", "purchase=" . $_POST['item']);
	$dbc->Delete("bs_purchase_spot_profit_bwd", "purchase=" . $_POST['item']);

	$dbc->Delete("bs_smg_trade", "purchase_spot=" . $_POST['item']);
	$dbc->Delete("bs_purchase_buy", "purchase_spot=" . $_POST['item']);

	$os->save_log(0, $_SESSION['auth']['user_id'], "spot-delete", $id, array("spot" => $spot));
} else {

	foreach ($_POST['items'] as $item) {
		$spot = $dbc->GetRecord("bs_purchase_spot", "*", "id=" . $item);
		$dbc->Delete("bs_purchase_spot", "id=" . $item);
		$dbc->Delete("bs_purchase_spot_profit", "purchase=" . $item);
		$dbc->Delete("bs_purchase_spot_profit_bwd", "purchase=" . $item);
		$dbc->Delete("bs_smg_trade", "purchase_spot=" . $item);
		$dbc->Delete("bs_purchase_buy", "purchase_spot=" . $item);

		$os->save_log(0, $_SESSION['auth']['user_id'], "spot-delete", $id, array("spot" => $spot));
	}
}

$dbc->Close();
