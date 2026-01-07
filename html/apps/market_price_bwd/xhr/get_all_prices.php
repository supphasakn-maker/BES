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

$platforms = ['Tiktok', 'Shopee', 'Lazada'];
$prices = array();

foreach ($platforms as $platform) {
    $prices[$platform] = $os->load_variable($platform);
}

echo json_encode(array(
    'success' => true,
    'prices' => $prices,
    'updated_time' => date('Y-m-d H:i:s')
));

$dbc->Close();
