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

$round = $_POST['round'] ?? '';
$products = $dbc->GetRecord("bs_productions", "*", "round='" . $round . "'");

$amount = 0;
if ($products && isset($products['weight_out_total'])) {
    $amount = floatval($products['weight_out_total']);
}

echo json_encode(array(
    'products' => array(
        'amount' => $amount
    ),
    'debug' => array(
        'round' => $round,
        'found_record' => $products ? true : false,
        'raw_amount' => $products['weight_out_total'] ?? 'not found'
    )
));

$dbc->Close();
