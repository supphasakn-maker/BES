<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();
global $dbc;

$output = array();
if (isset($_POST['date_filter'])) {
    $date_filter = $dbc->real_escape_string($_POST['date_filter']);

    $sql_spot_usd = "SELECT SUM(bs_purchase_spot_profit.amount*32.1507*(bs_purchase_spot_profit.rate_spot+bs_purchase_spot_profit.rate_pmdc)) AS spot_usd FROM bs_purchase_spot_profit 
                     WHERE (bs_purchase_spot_profit.value_date = '" .  $date_filter . "') 
                     AND bs_purchase_spot_profit.parent IS NULL 
                     AND flag_hide = 0
                     AND bs_purchase_spot_profit.rate_spot > 0
                     AND (bs_purchase_spot_profit.type LIKE 'physical' OR bs_purchase_spot_profit.type LIKE 'MTM') 
                     AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
                     AND YEAR(date) > 2024 
                     AND bs_purchase_spot_profit.currency = 'USD'";
    $rst_spot_usd = $dbc->Query($sql_spot_usd);
    $row_spot_usd = $dbc->Fetch($rst_spot_usd);
    $spot_usd = isset($row_spot_usd['spot_usd']) ? (float)$row_spot_usd['spot_usd'] : 0;

    $sql_usd_amount = "SELECT SUM(amount) AS usd_amount FROM bs_purchase_usd_profit 
                       WHERE (bs_purchase_usd_profit.value_date = '" .  $date_filter . "') 
                       AND (bs_purchase_usd_profit.status <> -1) 
                       AND (bs_purchase_usd_profit.type LIKE 'physical' OR bs_purchase_usd_profit.type LIKE 'MTM')
                       AND YEAR(date) > 2024";
    $rst_usd_amount = $dbc->Query($sql_usd_amount);
    $row_usd_amount = $dbc->Fetch($rst_usd_amount);
    $usd_amount = isset($row_usd_amount['usd_amount']) ? (float)$row_usd_amount['usd_amount'] : 0;

    // คำนวณ difference
    $difference = $spot_usd - $usd_amount;

    $output[] = array(
        'purchase_spot_usd' => number_format($spot_usd, 4),
        'purchase_usd_true' => number_format($usd_amount, 4),
        'difference' => number_format($difference, 4)
    );
}

echo json_encode($output);
$dbc->Close();
