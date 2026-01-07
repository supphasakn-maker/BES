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

$rates = array(
    'Tiktok' => $os->load_variable("Tiktok"),
    'Shopee' => $os->load_variable("Shopee"),
    'Lazada' => $os->load_variable("Lazada")
);

echo json_encode(array(
    'success' => true,
    'rates' => $rates,
    'updated_time' => date('Y-m-d H:i:s')
));

$dbc->Close();
