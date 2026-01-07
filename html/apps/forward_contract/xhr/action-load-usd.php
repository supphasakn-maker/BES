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


function load_data($dbc, $id)
{
	$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $id);
	return array(
		"id" => $usd['id'],
		"bank" => $usd['bank'],
		"amount" => floatval($usd['amount']),
		"rate_exchange" => floatval($usd['rate_finance']),
		"type" => $usd['type'],
		"comment" => $usd['comment'],
		"date" => $usd['date'],
		"total" => floatval($usd['rate_finance'] * $usd['amount'])


	);
}

if (is_array($_POST['id'])) {
	$array = array();
	foreach ($_POST['id'] as $id) {
		array_push($array, load_data($dbc, $id));
	}
	echo json_encode($array);
} else {
	echo json_encode(load_data($dbc, $_POST['id']));
}


$dbc->Close();
