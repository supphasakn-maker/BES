<?php
$previousdate = strtotime("$_POST[date] ,-1 days");
$itoday = strtotime("$_POST[date]");
$start_date = "2022-05-05";
$iitoday = time();

global $dbc;

$start_date = strtotime("2022-05-05");
$today = strtotime($_POST['date']);
$previousdate = $today - 86400;

function calculateStockImproved($dbc, $product_id, $initial_balance, $start_date, $today, $previousdate)
{
	$aBalance = $initial_balance;

	$startDateStr = date("Y-m-d", $start_date - 86400);
	$todayStr = date("Y-m-d", $today - 86400);
	$currentDateStr = date("Y-m-d", $today);

	$line_production = $dbc->GetRecord(
		"bs_productions",
		"SUM(weight_out_packing)",
		"submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$product_id}"
	);
	$production_amount = isset($line_production[0]) ? floatval($line_production[0]) : 0;
	$aBalance += $production_amount;

	// เพิ่ม: stock in จากการรับเข้า
	$line_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"SUM(weight_out_total)",
		"submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$product_id}"
	);
	$stockin_amount = isset($line_stockin[0]) ? floatval($line_stockin[0]) : 0;
	$aBalance += $stockin_amount;

	$line_stocksilver = $dbc->GetRecord(
		"bs_stock_silver",
		"SUM(weight_actual)",
		"submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$product_id}"
	);
	$stocksilver_amount = isset($line_stocksilver[0]) ? floatval($line_stocksilver[0]) : 0;
	$aBalance += $stocksilver_amount;

	$line_MR = $dbc->GetRecord(
		"bs_productions_pmr",
		"SUM(weight_out_packing)",
		"submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$product_id}"
	);
	$mr_amount = isset($line_MR[0]) ? floatval($line_MR[0]) : 0;
	$aBalance -= $mr_amount;

	$line_order = $dbc->GetRecord(
		"bs_orders",
		"SUM(amount)",
		"delivery_date BETWEEN '{$startDateStr}' AND '{$todayStr}' AND status > 0 AND product_id = {$product_id}"
	);
	$order_amount = isset($line_order[0]) ? floatval($line_order[0]) : 0;
	$aBalance -= $order_amount;

	$sql = "SELECT id FROM bs_stock_adjuest_types WHERE type = 1 AND id != 9 ORDER BY sort_id";
	$rst_deposit_types = $dbc->Query($sql);
	if ($rst_deposit_types) {
		while ($type_item = $dbc->Fetch($rst_deposit_types)) {
			$typeId = intval($type_item['id']);
			$line = $dbc->GetRecord(
				"bs_stock_adjusted",
				"SUM(amount)",
				"date BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$product_id} AND type_id = {$typeId}"
			);
			$daily_adjusted_amount = isset($line[0]) ? floatval($line[0]) : 0;
			$aBalance += $daily_adjusted_amount;
		}
	}

	$today_production = $dbc->GetRecord(
		"bs_productions",
		"SUM(weight_out_packing)",
		"submited LIKE '{$currentDateStr}' AND product_id = {$product_id}"
	);
	$today_production_amount = isset($today_production[0]) ? floatval($today_production[0]) : 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"SUM(weight_out_total)",
		"submited LIKE '{$currentDateStr}' AND product_id = {$product_id}"
	);
	$today_stockin_amount = isset($today_stockin[0]) ? floatval($today_stockin[0]) : 0;

	$today_total_in = $today_production_amount + $today_stockin_amount;
	$aBalance += $today_total_in;

	$today_stocksilver = $dbc->GetRecord(
		"bs_stock_silver",
		"SUM(weight_actual)",
		"submited LIKE '{$currentDateStr}' AND product_id = {$product_id}"
	);
	$today_stocksilver_amount = isset($today_stocksilver[0]) ? floatval($today_stocksilver[0]) : 0;
	$aBalance += $today_stocksilver_amount;

	$today_pmr = $dbc->GetRecord(
		"bs_productions_pmr",
		"SUM(weight_out_packing)",
		"submited LIKE '{$currentDateStr}' AND product_id = {$product_id}"
	);
	$today_pmr_amount = isset($today_pmr[0]) ? floatval($today_pmr[0]) : 0;
	$aBalance -= $today_pmr_amount;

	$sql = "SELECT id FROM bs_stock_adjuest_types WHERE type = 1 AND id != 9 ORDER BY sort_id";
	$rst_deposit_types = $dbc->Query($sql);
	if ($rst_deposit_types) {
		while ($type_item = $dbc->Fetch($rst_deposit_types)) {
			$typeId = intval($type_item['id']);
			$line = $dbc->GetRecord(
				"bs_stock_adjusted",
				"SUM(amount)",
				"date = '{$currentDateStr}' AND product_id = {$product_id} AND type_id = {$typeId}"
			);
			$daily_amount = isset($line[0]) ? floatval($line[0]) : 0;
			$aBalance += $daily_amount;
		}
	}

	$memo_buffer = 0;
	$sql = "SELECT id FROM bs_stock_adjuest_types WHERE type = 2 ORDER BY sort_id";
	$rst_memo_types = $dbc->Query($sql);
	if ($rst_memo_types) {
		while ($type_item = $dbc->Fetch($rst_memo_types)) {
			$typeId = intval($type_item['id']);
			$line = $dbc->GetRecord(
				"bs_stock_adjusted",
				"SUM(amount)",
				"date <= '{$currentDateStr}' AND product_id = {$product_id} AND type_id = {$typeId}"
			);
			$memo_amount = isset($line[0]) ? floatval($line[0]) : 0;
			$memo_buffer += $memo_amount;
		}
	}

	$line_type12 = $dbc->GetRecord(
		"bs_stock_adjusted",
		"SUM(amount2)",
		"date <= '{$currentDateStr}' AND product_id = {$product_id} AND type_id = 12"
	);
	$type12_amount2 = isset($line_type12[0]) ? floatval($line_type12[0]) : 0;

	$today_deliveries = $dbc->GetRecord(
		"bs_orders",
		"SUM(amount)",
		"delivery_date = '{$currentDateStr}' AND status > 0 AND product_id = {$product_id}"
	);
	$today_delivery_amount = isset($today_deliveries[0]) ? floatval($today_deliveries[0]) : 0;

	$special_adjustment = 0;
	if ($product_id == 1) {
		$vv = $dbc->GetRecord(
			"bs_stock_adjusted",
			"SUM(amount)",
			"date <= '{$currentDateStr}' AND product_id = 1 AND type_id = 1"
		);
		$special_adjustment = isset($vv[0]) ? floatval($vv[0]) : 0;
	}

	return array(
		'total_balance' => $aBalance + $memo_buffer + $type12_amount2,
		'today_balance' => $aBalance + $today_total_in - $today_pmr_amount,
		'final_balance' => $aBalance + $today_total_in - $special_adjustment - $today_delivery_amount,
		'today_production' => $today_total_in,
		'today_pmr' => $today_pmr_amount,
		'special_adjustment' => $special_adjustment,
		'today_deliveries' => $today_delivery_amount
	);
}

function calculateSilverStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date; // วันปัจจุบัน
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day")); // วันก่อนหน้า

	$productions_yesterday = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '1' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$productions_yesterday_amount = ($productions_yesterday[0] ?? 0) + 1204;

	$stockin_yesterday = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '1' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$stockin_yesterday_amount = $stockin_yesterday[0] ?? 0;

	$orders_past = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '1' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$orders_past_amount = $orders_past[0] ?? 0;

	$stock_adj_type1 = $dbc->GetRecord(
		"bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id",
		"COALESCE(SUM(bs_stock_adjusted.amount),0)",
		"bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.date BETWEEN '2022-03-29' AND '{$sql_date2}'"
	);
	$stock_adj_type1_amount = $stock_adj_type1[0] ?? 0;

	$stock_adj_type2 = $dbc->GetRecord(
		"bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id",
		"COALESCE(SUM(bs_stock_adjusted.amount),0)",
		"bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 1 AND bs_stock_adjusted.type_id = 3 AND bs_stock_adjusted.date BETWEEN '2022-03-29' AND '{$sql_date2}'"
	);
	$stock_adj_type2_amount = $stock_adj_type2[0] ?? 0;

	$special_adj = $dbc->GetRecord(
		"bs_stock_adjusted",
		"COALESCE(SUM(amount),0)",
		"product_id = '1' AND date = '2023-12-22'"
	);
	$special_adj_amount = $special_adj[0] ?? 0;

	$pmr_productions_yesterday = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '1' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$pmr_productions_yesterday_amount = $pmr_productions_yesterday[0] ?? 0;

	$yesterday_balance = $productions_yesterday_amount
		+ $stockin_yesterday_amount // เพิ่มส่วนนี้
		- $orders_past_amount
		- $stock_adj_type1_amount
		+ $stock_adj_type2_amount
		- $special_adj_amount
		- $pmr_productions_yesterday_amount;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '1' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '1' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '1' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '1' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateBarStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$productions_yesterday = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '2' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$productions_yesterday_amount = ($productions_yesterday[0] ?? 0) + 53;

	$stockin_yesterday = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '2' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$stockin_yesterday_amount = $stockin_yesterday[0] ?? 0;

	$orders_past = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '2' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$orders_past_amount = $orders_past[0] ?? 0;

	$stock_silver_yesterday = $dbc->GetRecord(
		"bs_stock_silver",
		"COALESCE(SUM(weight_expected),0)",
		"product_id = '2' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}'"
	);
	$stock_silver_yesterday_amount = $stock_silver_yesterday[0] ?? 0;

	$yesterday_balance = $productions_yesterday_amount + $stockin_yesterday_amount - $orders_past_amount + $stock_silver_yesterday_amount;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '2' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '2' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_stock_silver = $dbc->GetRecord(
		"bs_stock_silver",
		"COALESCE(SUM(weight_expected),0)",
		"product_id = '2' AND submited = '{$sql_date}'"
	);
	$today_stock_silver_amount = $today_stock_silver[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '2' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount + $today_stock_silver_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_stock_silver' => $today_stock_silver_amount,
		'today_orders' => $today_orders_amount,
		'productions_yesterday' => $productions_yesterday_amount + $stockin_yesterday_amount,
		'orders_past' => $orders_past_amount,
		'stock_silver_yesterday' => $stock_silver_yesterday_amount
	);
}

function calculateLBMAStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0)+206.7689 
             FROM bs_productions 
             WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '3' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '3' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '3' AND submited = '2024-01-17')
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '3' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '3' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '3' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '3' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateRECYCLEStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0)+0.018 
             FROM bs_productions 
             WHERE product_id = '4' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '4' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 4 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 4 AND bs_stock_adjusted.type_id = 9 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '4' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '4' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '4' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '4' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '4' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '4' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateOriginThaiStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions 
             WHERE product_id = '5' AND submited BETWEEN '2024-02-22' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '5' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted 
             WHERE product_id = '5' AND date BETWEEN '2024-05-01' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_expected),0) 
             FROM bs_stock_silver 
             WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '5' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
            + 1.3003
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '5' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '5' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_stock_adjusted = $dbc->GetRecord(
		"bs_stock_adjusted",
		"COALESCE(SUM(amount),0)",
		"product_id = '5' AND date = '{$sql_date}'"
	);
	$today_stock_adjusted_amount = $today_stock_adjusted[0] ?? 0;

	$today_stock_silver = $dbc->GetRecord(
		"bs_stock_silver",
		"COALESCE(SUM(weight_expected),0)",
		"product_id = '5' AND submited = '{$sql_date}'"
	);
	$today_stock_silver_amount = $today_stock_silver[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '5' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '5' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount + $today_stock_adjusted_amount + $today_stock_silver_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_stock_adjusted' => $today_stock_adjusted_amount,
		'today_stock_silver' => $today_stock_silver_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateSilverArticleStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0)+0.000 
             FROM bs_productions 
             WHERE product_id = '8' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '8' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 8 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 8 AND bs_stock_adjusted.type_id = 9 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '8' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '8' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '8' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '8' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '8' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateLocalRecycleStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0)+0.000 
             FROM bs_productions 
             WHERE product_id = '7' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '7' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 7 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 7 AND bs_stock_adjusted.type_id = 9 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '7' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '7' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '7' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '7' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '7' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '7' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

function calculateSilverAPlateStockCorrect($dbc, $current_date)
{
	$sql_date = $current_date;
	$sql_date2 = date("Y-m-d", strtotime($current_date . " -1 day"));

	$yesterday_balance_query = "
        SELECT 
        (
            (SELECT COALESCE(SUM(weight_out_packing),0)+0.000 
             FROM bs_productions 
             WHERE product_id = '6' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(weight_out_total),0) 
             FROM bs_productions_in 
             WHERE product_id = '6' AND submited BETWEEN '2023-02-27' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 1 AND bs_stock_adjusted.product_id = 6 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            +
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_stock_adjusted LEFT JOIN bs_stock_adjuest_types ON bs_stock_adjusted.type_id = bs_stock_adjuest_types.id 
             WHERE bs_stock_adjuest_types.type = 2 AND bs_stock_adjusted.product_id = 6 AND bs_stock_adjusted.type_id = 9 AND date BETWEEN '2022-03-29' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(amount),0) 
             FROM bs_orders 
             WHERE product_id = '6' AND status > 0 AND delivery_date BETWEEN '2022-05-04' AND '{$sql_date2}')
            -
            (SELECT COALESCE(SUM(weight_out_packing),0) 
             FROM bs_productions_pmr 
             WHERE product_id = '8' AND submited BETWEEN '2022-05-04' AND '{$sql_date2}')
        ) as yesterday_balance
    ";

	$yesterday_result = $dbc->Query($yesterday_balance_query);
	$yesterday_row = $dbc->Fetch($yesterday_result);
	$yesterday_balance = $yesterday_row['yesterday_balance'] ?? 0;

	$today_productions = $dbc->GetRecord(
		"bs_productions",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '6' AND submited = '{$sql_date}'"
	);
	$today_productions_amount = $today_productions[0] ?? 0;

	$today_stockin = $dbc->GetRecord(
		"bs_productions_in",
		"COALESCE(SUM(weight_out_total),0)",
		"product_id = '6' AND submited = '{$sql_date}'"
	);
	$today_stockin_amount = $today_stockin[0] ?? 0;

	$today_pmr_productions = $dbc->GetRecord(
		"bs_productions_pmr",
		"COALESCE(SUM(weight_out_packing),0)",
		"product_id = '6' AND submited = '{$sql_date}'"
	);
	$today_pmr_productions_amount = $today_pmr_productions[0] ?? 0;

	$today_orders = $dbc->GetRecord(
		"bs_orders",
		"COALESCE(SUM(amount),0)",
		"product_id = '6' AND status > 0 AND delivery_date = '{$sql_date}'"
	);
	$today_orders_amount = $today_orders[0] ?? 0;

	$current_balance = $yesterday_balance + $today_productions_amount + $today_stockin_amount - $today_pmr_productions_amount - $today_orders_amount;

	return array(
		'yesterday_balance' => $yesterday_balance,
		'current_balance' => $current_balance,
		'today_productions' => $today_productions_amount + $today_stockin_amount,
		'today_pmr_productions' => $today_pmr_productions_amount,
		'today_orders' => $today_orders_amount
	);
}

$current_date_str = date("Y-m-d", $today);
$lbma_data_correct = calculateLBMAStockCorrect($dbc, $current_date_str);
$recycle_data_correct = calculateRECYCLEStockCorrect($dbc, $current_date_str);
$origin_thai_data_correct = calculateOriginThaiStockCorrect($dbc, $current_date_str);
$silver_article_data_correct = calculateSilverArticleStockCorrect($dbc, $current_date_str);
$silver_plate_data_correct = calculateSilverAPlateStockCorrect($dbc, $current_date_str);
$local_recycle_data_correct = calculateLocalRecycleStockCorrect($dbc, $current_date_str); // เพิ่มบรรทัดนี้
?>

<!DOCTYPE html>
<html lang="th">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>รายงานสต็อกสินค้า</title>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		:root {
			--primary-color: #00204E;
			--secondary-color: #ffffff;
			--accent-color: #f8f9fa;
			--border-color: #e9ecef;
			--shadow-color: rgba(0, 32, 78, 0.1);
		}

		html,
		body {
			margin: 0;
			padding: 0;
			width: 100%;
			overflow-x: auto;
		}

		body {
			background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
			min-height: 100vh;
			padding: 5px 0;
		}

		.container-fluid {
			width: 100%;
			max-width: none;
			margin: 0;
			padding: 0 8px;
		}

		.row {
			margin: 0;
		}

		.col-sm-4 {
			padding: 0 3px;
		}

		.section-title {
			background: var(--primary-color);
			color: var(--secondary-color);
			padding: 8px 15px;
			margin: 10px 0 8px 0;
			border-radius: 8px;
			font-weight: 600;
			font-size: 16px;
			text-align: center;
			box-shadow: 0 4px 15px var(--shadow-color);
		}

		.table-container {
			background: var(--secondary-color);
			border-radius: 12px;
			box-shadow: 0 8px 25px var(--shadow-color);
			margin-bottom: 10px;
			overflow: hidden;
			border: 1px solid var(--border-color);
		}

		.custom-header {
			background: linear-gradient(135deg, var(--primary-color) 0%, #003a7a 100%);
			color: var(--secondary-color);
			font-weight: 600;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			padding: 12px;
			border: none;
			position: relative;
		}

		.custom-header::before {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			right: 0;
			height: 3px;
			background: linear-gradient(90deg, #ffd700, #ffed4e, #ffd700);
		}

		.table {
			margin-bottom: 0;
			font-size: 12px;
		}

		.table th,
		.table td {
			border: 1px solid var(--border-color);
			padding: 8px 6px;
			vertical-align: middle;
		}

		.table tbody tr:hover {
			background-color: rgba(0, 32, 78, 0.05);
			transition: all 0.3s ease;
		}

		.data-cell {
			background-color: #f8f9fa;
			font-weight: 500;
			color: var(--primary-color);
		}

		.summary-row {
			background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
			font-weight: 600;
			color: var(--primary-color);
		}

		.summary-row th {
			border-top: 2px solid var(--primary-color);
		}

		.balance-positive {
			color: #28a745;
			font-weight: 600;
		}

		.balance-negative {
			color: #dc3545;
			font-weight: 600;
		}

		.number-display {
			font-weight: 600;
			text-align: right;
			background-color: rgba(0, 32, 78, 0.05);
			border-radius: 4px;
			padding: 5px 8px;
		}

		.table-sm th,
		.table-sm td {
			padding: 8px 6px;
		}

		.text-center {
			text-align: center !important;
		}

		.font-weight-bold {
			font-weight: 700 !important;
		}

		.text-white {
			color: white !important;
		}

		.bg-dark {
			background: linear-gradient(135deg, var(--primary-color) 0%, #003a7a 100%) !important;
			color: var(--secondary-color) !important;
		}

		@media (max-width: 768px) {
			.col-sm-4 {
				margin-bottom: 15px;
			}

			.table {
				font-size: 10px;
			}

			.custom-header {
				font-size: 11px;
			}
		}

		@media (min-width: 1200px) {
			.table {
				font-size: 13px;
			}

			.custom-header {
				font-size: 14px;
			}
		}

		/* Animation for tables */
		.table-container {
			animation: fadeInUp 0.6s ease-out;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.highlight-cell {
			background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
			font-weight: 600;
			color: var(--primary-color);
		}

		.number-cell {
			font-weight: 600;
			text-align: center;
			background-color: rgba(0, 32, 78, 0.03);
			color: var(--primary-color);
		}
	</style>
</head>

<body>

	<div class="container-fluid">

		<div class="row">
			<div class="col-sm-4">
				<div class="table-container">
					<table class="table table-sm table-bordered">
						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า SILVER (Production + Stock In)</th>
							</tr>
							<tr class="bg-dark">
								<th class="text-center font-weight-bold text-white">DATE</th>
								<th class="text-center font-weight-bold text-white">SUPPLIER</th>
								<th class="text-center font-weight-bold text-white">BAG X KG.</th>
								<th class="text-center font-weight-bold text-white">KG.</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 1");
							$line_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 1");
							$line_total = ($line_production[0] ?? 0) + ($line_stockin[0] ?? 0);
							?>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line_total, 4); ?></td>
							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า LBMA (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line1_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 3");
							$line1_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 3");
							$line1_total = ($line1_production[0] ?? 0) + ($line1_stockin[0] ?? 0);
							$linesale = $dbc->GetRecord("bs_stock_silver", "SUM(weight_actual)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 3");
							?>
							<tr style="height: 60px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line1_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line1_total, 4); ?></td>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($linesale[0], 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($linesale[0], 4); ?></td>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า RECYCLE (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line2_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 4");
							$line2_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 4");
							$line2_total = ($line2_production[0] ?? 0) + ($line2_stockin[0] ?? 0);
							?>
							<tr style="height: 75px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line2_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line2_total, 4); ?></td>

							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>

							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า ORIGIN THAI (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line3_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 5");
							$line3_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 5");
							$line3_total = ($line3_production[0] ?? 0) + ($line3_stockin[0] ?? 0);
							?>
							<tr style="height: 75px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line3_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line3_total, 4); ?></td>

							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>

							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า SILVER PLATE (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line4_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 6");
							$line4_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 6");
							$line4_total = ($line4_production[0] ?? 0) + ($line4_stockin[0] ?? 0);
							?>
							<tr style="height: 75px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line4_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line4_total, 4); ?></td>

							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>

							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า LOCAL RECYCLE (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line5_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 7");
							$line5_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 7");
							$line5_total = ($line5_production[0] ?? 0) + ($line5_stockin[0] ?? 0);
							?>
							<tr style="height: 75px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line5_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line5_total, 4); ?></td>

							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>

							</tr>
						</tbody>
						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้า SILVER ARTICLE (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line8_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 8");
							$line8_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 8");
							$line8_total = ($line8_production[0] ?? 0) + ($line8_stockin[0] ?? 0);
							?>
							<tr style="height: 75px;">
								<th class="col-sm-3 text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line8_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line8_total, 4); ?></td>

							</tr>
							<tr style="height: 85px;">
								<th class="col-sm-3 text-center"></th>
								<td class="text-center"></td>
								<td class="text-center"></td>
								<td class="text-center"></td>

							</tr>
						</tbody>
						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ส่งผลิตแท่ง ALL PRODUCTS</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumPMRtosilver_left = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' ");
							?>
							<tr style="height: 60px;">
								<th class="text-center highlight-cell" colspan="3">ส่งผลิตแท่งรวมทั้งหมด</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumPMRtosilver_left[0], 4); ?></th>
							</tr>
						</tbody>

						<?php
						$aaline = 0;
						$aaline += $line_total + $line1_total + $line2_total + $line3_total + $line4_total + $line5_total + $line8_total;
						?>
						<tr class="summary-row">
							<th class="text-center" colspan="3">ยอดของเข้า เม็ดเงิน Silver + LBMA + RECYCLE + ORIGIN THAI + SILVER PLATE + LOCAL RECYCLE + SILVER ARTICLE</th>
							<th class="text-center number-display" colspan="1"><?php echo number_format($aaline, 4); ?></th>
						</tr>

						<thead>
							<tr class="bg-dark">
								<th colspan="4" class="text-center font-weight-bold text-white">ขาเข้าแท่ง BWS + BWF (Production + Stock In)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$line_bar_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 2");
							$line_bar_stockin = $dbc->GetRecord("bs_productions_in", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 2");
							$line_bar_total = ($line_bar_production[0] ?? 0) + ($line_bar_stockin[0] ?? 0);
							$linesale_bar = $dbc->GetRecord("bs_stock_silver", "SUM(weight_actual)", "submited LIKE '" . $_POST['date'] . "' AND product_id = 2");
							?>
							<tr>
								<th class="text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($line_bar_total, 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($line_bar_total, 4); ?></td>

							</tr>
							<tr>
								<th class="text-center data-cell"><?php echo $_POST['date']; ?></th>
								<td class="text-center"></td>
								<td class="text-center number-cell"><?php echo number_format($linesale_bar[0], 4); ?></td>
								<td class="text-center number-cell"><?php echo number_format($linesale_bar[0], 4); ?></td>

							</tr>
						</tbody>
						<tfoot>
							<tr class="summary-row">
								<?php
								$total_in_all = $aaline + $line_bar_total + $linesale_bar[0];
								?>
								<th class="text-center" colspan="3">ยอดของเข้าทั้งหมดตามระบบ</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($total_in_all, 4); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="table-container">
					<table class="table table-sm table-bordered">
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่งเม็ดเงิน Silver</th>
							</tr>
							<tr class="bg-dark">
								<th class="text-center font-weight-bold text-white">DETAIL</th>
								<th class="text-center font-weight-bold text-white">KG.</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sum = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=1");
							$sumpmrSilver = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id=1");
							?>
							<tr style="height: 85px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งเม็ดเงิน Silver ทั้งหมด</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sum[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง ออก PMR (SILVER)</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumpmrSilver[0], 4); ?></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่งเม็ด LBMA</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumPMR = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=3");
							$sumpmrLBMA = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id=3");
							?>
							<tr style="height: 60px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งเม็ด LBMA</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumPMR[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง ออก PMR (LBMA)</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumpmrLBMA[0], 4); ?></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่งเม็ด RECYCLE</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumRECYCLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=4");
							$sumpmrRECYCLE = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id=4");
							$sumpmrRRECYCLE = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_packing)", "submited LIKE '" . $_POST['date'] . "' AND product_id=4");
							?>
							<tr style="height: 75px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งเม็ด RECYCLE</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumRECYCLE[0], 4); ?></th>
							</tr>
							<tr style="height: 45px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง ผลิตเม็ด(RECYCLE)</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumpmrRECYCLE[0], 4); ?></th>
							</tr>
							<tr style="height: 40px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งออก PMR </th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumpmrRRECYCLE[0], 4); ?></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่ง ORIGIN THAI</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumThai = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=5");
							$sumpmrTHAI = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' AND product_id=5");
							?>
							<tr style="height: 75px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง ORIGIN THAI</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumThai[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง ออก PMR (ORIGIN THAI)</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumpmrTHAI[0], 4); ?></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่ง SILVER PLATE</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumSilverPlate = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=6");
							?>
							<tr style="height: 75px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง SILVER PLATE</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumSilverPlate[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center" colspan="1"></th>
								<th class="text-center" colspan="1"></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่ง LOCAL RECYCLE</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumSilverLOCALRECYCLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=7");
							?>
							<tr style="height: 75px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง LOCAL RECYCLE</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumSilverLOCALRECYCLE[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center" colspan="1"></th>
								<th class="text-center" colspan="1"></th>
							</tr>
						</tbody>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่ง SILVER ARTICLE</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumSilverSILVERARTICLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=8");
							?>
							<tr style="height: 75px;">
								<th class="text-center data-cell" colspan="1">ยอดรวมส่ง SILVER ARTICLE</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumSilverSILVERARTICLE[0], 4); ?></th>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center" colspan="1"></th>
								<th class="text-center" colspan="1"></th>
							</tr>
						</tbody>
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">ส่งผลิตแท่ง ALL PRODUCTS</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumPMRtosilver = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' ");
							?>
							<tr style="height: 60px;">
								<th class="text-center highlight-cell" colspan="1">ส่งผลิตแท่งรวมทั้งหมด</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumPMRtosilver[0], 4); ?></th>
							</tr>
						</tbody>

						<?php
						$sum = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=1");
						$sumPMR = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=3");
						$sumRECYCLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=4");
						$sumThai = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=5");
						$sumSilverPlate = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=6");
						$sumSilverLOCALRECYCLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=7");
						$sumSilverSILVERARTICLE = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=8");

						$sumBAR = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=2");
						$aAllnotBAR = 0;
						$aAllnotBAR += $sum[0] + $sumPMR[0] + $sumRECYCLE[0] + $sumThai[0] + $sumSilverPlate[0] + $sumSilverLOCALRECYCLE[0] + $sumSilverSILVERARTICLE[0];
						?>
						<tr class="summary-row">
							<th class="text-center" colspan="1">ยอดส่งของ เม็ดเงิน Silver + LBMA + RECYCLE + ORIGIN THAI + SILVER PLATE + LOCAL RECYCLE + SILVER ARTICLE</th>
							<th class="text-center number-display" colspan="1"><?php echo number_format($aAllnotBAR, 4); ?></th>
						</tr>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">รายละเอียดส่งออกแท่ง BWS + BWF</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumBAR = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '" . $_POST['date'] . "' AND status >0 AND product_id=2");
							?>
							<tr>
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งออกแท่ง</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumBAR[0], 4); ?></th>
							</tr>
							<?php
							$sumAllBAR = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' ");
							?>
							<tr>
								<th class="text-center data-cell" colspan="1">ยอดรวมส่งผลิตแท่ง</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumAllBAR[0], 4); ?></th>
							</tr>
						</tbody>
						<tfoot>
							<tr class="summary-row">
								<?php
								$aAllTotal = 0;
								$aAllTotal += $sum[0] + $sumPMR[0] + $sumRECYCLE[0] + $sumThai[0] + $sumSilverPlate[0] + $sumBAR[0] + $sumSilverLOCALRECYCLE[0] + $sumSilverSILVERARTICLE[0];
								?>
								<th class="text-center" colspan="1">ยอดส่งของทั้งหมดตามระบบ</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($aAllTotal, 4); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="table-container">
					<table class="table table-sm table-bordered">
						<!-- Silver Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือเม็ดเงิน Silver</th>
							</tr>
							<tr class="bg-dark">
								<th class="text-center font-weight-bold text-white">DETAIL</th>
								<th class="text-center font-weight-bold text-white">BALANCE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 85px;">
								<td class="text-center data-cell">ยอดยกมาเม็ดเงิน Silver</td>
								<?php
								$current_date_str = date("Y-m-d", $today);
								$silver_data_correct = calculateSilverStockCorrect($dbc, $current_date_str);
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($silver_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK เม็ด SILVER</th>
								<?php
								$balance_class = $silver_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($silver_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- LBMA Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือเม็ด LBMA</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 60px;">
								<td class="text-center data-cell">ยอดยกมาเม็ด LBMA</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($lbma_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK เม็ด LBMA</th>
								<?php
								$balance_class = $lbma_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($lbma_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- RECYCLE Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือเม็ด RECYCLE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 75px;">
								<td class="text-center data-cell">ยอดยกมาเม็ด RECYCLE</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($recycle_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK RECYCLE</th>
								<?php
								$balance_class = $recycle_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($recycle_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- ORIGIN THAI Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือ ORIGIN THAI</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 75px;">
								<td class="text-center data-cell">ยอดยกมา ORIGIN THAI</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($origin_thai_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK ORIGIN THAI</th>
								<?php
								$balance_class = $origin_thai_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($origin_thai_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- SILVER PLATE Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือ SILVER PLATE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 75px;">
								<td class="text-center data-cell">ยอดยกมา SILVER PLATE</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($silver_plate_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK SILVER PLATE</th>
								<?php
								$balance_class = $silver_plate_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($silver_plate_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- LOCAL RECYCLE Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือ LOCAL RECYCLE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 75px;">
								<td class="text-center data-cell">ยอดยกมา LOCAL RECYCLE</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($local_recycle_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK LOCAL RECYCLE</th>
								<?php
								$balance_class = $local_recycle_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($local_recycle_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- SILVER ARTICLE Section - ใช้ฟังก์ชันใหม่ -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือ SILVER ARTICLE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 75px;">
								<td class="text-center data-cell">ยอดยกมา SILVER ARTICLE</td>
								<?php
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($silver_article_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr style="height: 85px;">
								<th class="text-center highlight-cell">TOTAL STOCK SILVER ARTICLE</th>
								<?php
								$balance_class = $silver_article_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($silver_article_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>

						<!-- ส่งผลิตแท่ง ALL PRODUCTS Section -->
						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">ส่งผลิตแท่ง ALL PRODUCTS</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sumPMRtosilver = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_total)", "submited LIKE '" . $_POST['date'] . "' ");
							?>
							<tr style="height: 60px;">
								<th class="text-center highlight-cell" colspan="1">ส่งผลิตแท่งรวมทั้งหมด</th>
								<th class="text-center number-display" colspan="1"><?php echo number_format($sumPMRtosilver[0], 4); ?></th>
							</tr>
						</tbody>

						<tr class="summary-row">
							<th class="text-left" colspan="1">คงเหลือเม็ดเงิน Silver + LBMA + RECYCLE + ORIGIN THAI + SILVER PLATE + LOCAL RECYCLE + SILVER ARTICLE</th>
							<th class="text-center" colspan="1">
								<?php
								$total_all = $silver_data_correct['current_balance'] + $lbma_data_correct['current_balance'] +
									$recycle_data_correct['current_balance'] + $origin_thai_data_correct['current_balance'] +
									$silver_plate_data_correct['current_balance'] + $local_recycle_data_correct['current_balance'] + $silver_article_data_correct['current_balance'];
								$balance_class = $total_all >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<span class="number-display ' . $balance_class . '">';
								echo number_format($total_all, 4);
								echo '</span>';
								?>
							</th>
						</tr>

						<thead>
							<tr class="bg-dark">
								<th colspan="2" class="text-center font-weight-bold text-white">คงเหลือแท่ง BWS + BWF</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-center data-cell">ยอดยกมาแท่ง 1 KG.</td>
								<?php
								$bar_data_correct = calculateBarStockCorrect($dbc, $current_date_str);
								echo '<td class="text-center number-display balance-positive">';
								echo number_format($bar_data_correct['yesterday_balance'], 4);
								echo '</td>';
								?>
							</tr>
							<tr>
								<th class="text-center highlight-cell">TOTAL STOCK แท่ง BWF + BWS</th>
								<?php
								$balance_class = $bar_data_correct['current_balance'] >= 0 ? 'balance-positive' : 'balance-negative';
								echo '<th class="text-center number-display ' . $balance_class . '">';
								echo number_format($bar_data_correct['current_balance'], 4);
								echo '</th>';
								?>
							</tr>
						</tbody>
						<tfoot>
							<tr class="summary-row">
								<?php
								$total_system = $total_all + $bar_data_correct['current_balance'];
								$balance_class = $total_system >= 0 ? 'balance-positive' : 'balance-negative';
								?>
								<th class="text-center" colspan="1">ยอดคงเหลือทั้งหมดตามระบบ</th>
								<th class="text-center" colspan="1">
									<span class="number-display <?php echo $balance_class; ?>">
										<?php echo number_format($total_system, 4); ?>
									</span>
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

</body>

</html>