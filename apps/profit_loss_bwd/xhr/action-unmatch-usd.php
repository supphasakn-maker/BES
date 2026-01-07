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

$silver_mapping = $dbc->GetRecord("bs_mapping_profit_orders_usd_bwd", "*", "id=" . $_POST['id']);
$dbc->Delete("bs_mapping_profit_orders_usd_bwd", "id=" . $_POST['id']);

$is_mapping = $dbc->HasRecord("bs_mapping_profit_orders_usd_bwd", "order_id = '" . $silver_mapping['order_id'] . "'");

$silversum = $dbc->GetRecord("bs_mapping_profit_orders_usd_bwd", "SUM(amount) AS amount, SUM(total) AS total", "id != " . $_POST['id'] . " AND mapping_id = " . $silver_mapping['mapping_id']);

if ($silversum['amount'] <= 0) {
    $dbc->Delete("bs_mapping_profit_sumusd_bwd", "id=" . $silver_mapping['mapping_id']);
} else {
    $dbc->Update("bs_mapping_profit_sumusd_bwd", array(
        "#amount" => $silversum['amount'],
        "#total" => $silversum['total']
    ), "id=" . $silver_mapping['mapping_id']);
}

echo json_encode(array(
    'success' => true
));

$dbc->Close();
