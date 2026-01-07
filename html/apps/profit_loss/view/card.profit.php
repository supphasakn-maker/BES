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

    // Query 1: ผลรวม total จาก bs_mapping_profit_sumusd
    $sql_usd = "SELECT SUM(bs_mapping_profit_orders_usd.total) AS totalordersusd FROM `bs_mapping_profit_sumusd` 
    LEFT OUTER JOIN  bs_mapping_profit_orders_usd ON bs_mapping_profit_orders_usd.mapping_id=bs_mapping_profit_sumusd.id 
    WHERE DATE(bs_mapping_profit_sumusd.mapped) = '" . $date_filter . "'";
    $rst_usd = $dbc->Query($sql_usd);
    $row_usd = $dbc->Fetch($rst_usd);
    $totalordersusd = (isset($row_usd['totalordersusd']) && $row_usd['totalordersusd'] != null  && $row_usd['totalordersusd'] != 0 && $row_usd['totalordersusd'] != '') ? (float)$row_usd['totalordersusd'] : 0; // Ensure it's a float
    $totalordersusd_formatted = number_format($totalordersusd, 4);

    //Query 2: ผลรวม total จาก bs_mapping_profit thaibath
    $sql_thb = "SELECT SUM(bs_mapping_profit_orders.total) AS totalordersthb FROM `bs_mapping_profit` 
    LEFT OUTER JOIN  bs_mapping_profit_orders ON bs_mapping_profit_orders.mapping_id=bs_mapping_profit.id 
    WHERE DATE(bs_mapping_profit.mapped) = '" . $date_filter . "'";
    $rst_thb = $dbc->Query($sql_thb);
    $row_thb = $dbc->Fetch($rst_thb);
    $totalordersthb = (isset($row_thb['totalordersthb']) && $row_thb['totalordersthb'] != null  && $row_thb['totalordersthb'] != 0 && $row_thb['totalordersthb'] != '') ? (float)$row_thb['totalordersthb'] : 0;  // Ensure it's a float
    $totalordersthb_formatted = number_format($totalordersthb, 4);

    //คำนวณผลรวม totalorders
    $totalorders = $totalordersusd; // Changed as per previous clarification
    $totalorders_formatted = number_format($totalorders, 4); // Format after addition


    // Query 3: ผลรวม usd true จาก bs_purchase_usd_profit
    $sql_usd_true = "SELECT SUM(amount * rate_finance) AS usd_true FROM bs_purchase_usd_profit WHERE (bs_purchase_usd_profit.value_date = '" .  $date_filter . "') AND (bs_purchase_usd_profit.status <> -1) AND (bs_purchase_usd_profit.type LIKE 'physical' OR bs_purchase_usd_profit.type LIKE 'MTM')
    AND YEAR(date) > 2024";
    $rst_usd_true = $dbc->Query($sql_usd_true);
    $row_usd_true = $dbc->Fetch($rst_usd_true);
    $usd_true = isset($row_usd_true['usd_true']) ? (float)$row_usd_true['usd_true'] : 0;  // Ensure it's a float
    $usd_true_formatted = number_format($usd_true, 4);
    $profit = number_format($totalorders - $usd_true, 4);

    $sql_thb_true = "SELECT SUM(THBValue) AS thb_true FROM bs_purchase_spot_profit WHERE (bs_purchase_spot_profit.value_date = '" .  $date_filter . "') AND bs_purchase_spot_profit.parent IS NULL 
                    AND flag_hide = 0
                    AND bs_purchase_spot_profit.rate_spot > 0
                    AND (bs_purchase_spot_profit.type LIKE 'physical'
    OR bs_purchase_spot_profit.type LIKE 'MTM') 
                    AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
                    AND YEAR(date) > 2024 AND bs_purchase_spot_profit.currency = 'THB'";
    $rst_thb_true = $dbc->Query($sql_thb_true);
    $row_thb_true = $dbc->Fetch($rst_thb_true);
    $thb_true = isset($row_thb_true['thb_true']) ? (float)$row_thb_true['thb_true'] : 0;  // Ensure it's a float
    $thb_true_formatted = number_format($thb_true, 4);
    $profit_thb = number_format($totalordersthb - $thb_true, 4);
    $total_profit = number_format(($totalorders - $usd_true) + ($totalordersthb - $thb_true), 2); //Corrected Calculation

    $output[] = array(
        'orders' => $totalorders_formatted,
        'purchase_usd' => $usd_true_formatted,
        'orders_thb' => $totalordersthb_formatted,
        'purchase_thb' => $thb_true_formatted,
        'profit' => $profit,
        'profit_thb' => $profit_thb,
        'total_profit' => $total_profit
    );
}
echo json_encode($output);

$dbc->Close();
