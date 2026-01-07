<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../include/const_standard.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);



$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$date = strtotime($_POST['date']);
$date_for_sql = $_POST['date'];
$newDate = date("Y-m-d", strtotime("-1 days", strtotime($date_for_sql)));

$order_condition = " bs_orders.parent IS NULL AND bs_orders.status > -1";


$sql = "SELECT
		COUNT(id) AS total_item, 
		SUM(amount) AS amount, 
		SUM(total) AS total,
		SUM(net) AS net
	FROM bs_orders 
	WHERE 
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND bs_orders.parent IS NULL 
		AND bs_orders.status > -1";
$rst = $dbc->Query($sql);
$order = $dbc->Fetch($rst);

$sql = "SELECT 
		COUNT(id) AS total_item, 
		SUM(amount) AS amount, 
		SUM((rate_spot+rate_pmdc)*amount*32.1507) AS total
	FROM bs_purchase_spot 
	WHERE
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND (bs_purchase_spot.type LIKE 'physical' OR bs_purchase_spot.type LIKE 'stock') 
		AND rate_spot > 0 AND status > 0 AND currency != 'THB'";
$rst = $dbc->Query($sql);
$purchase = $dbc->Fetch($rst);

$sql = "SELECT 
		COUNT(id) AS total_item, 
		SUM(amount) AS amount
	FROM bs_purchase_usd 
	WHERE 
		YEAR(date) > 2021
		AND DATE(date) <= '$date_for_sql'
		AND parent IS NULL AND status != 0";
$rst = $dbc->Query($sql);
$purchase_usd = $dbc->Fetch($rst);

$balance_amount = -$order['amount'] + $purchase['amount'] - 19;
$balance_usd = -$purchase['total'] + $purchase_usd['amount'] - 4710.36;


echo $balance_amount;
echo "|";
echo $balance_usd;



function replace_sql_string($text, $product_id = 0)
{
	$text = str_replace("#sql_date#", $_POST['date'], $text);
	$text = str_replace("#sql_date2#", date("Y-m-d", strtotime("-1 days", strtotime($_POST['date']))), $text);
	$text = str_replace("#product_id#", $product_id, $text);
	return $text;
}

$aSupplier = array();
$aSum = array();
$aSumPending = array();
$aSumPurchase = array();
$aSumUntake = array();
$TotalOnHand = 0;
$TotalFinal = 0;
$sql = "SELECT * FROM bs_suppliers_standard where status  =1";
$rst = $dbc->Query($sql);
while ($supplier = $dbc->Fetch($rst)) {
	array_push($aSupplier, array(
		"id" => $supplier['code'],
		"name" => $supplier['name'],
		"amount" => $supplier['amount']
	));
	array_push($aSum, array(0, 0));
	array_push($aSumPurchase, array(0, 0));
	array_push($aSumPending, array(0, 0));
	array_push($aSumUntake, array(0, 0));
}
echo '<div class="mb-3">';
echo '<table class="table table-sm table-bordered">';
echo '<thead style="background: linear-gradient(135deg, #00204E, #003366);">';
echo '<tr>';
echo '<th class="text-center text-white font-weight-bold" rowspan="2">' . $_POST['date'] . '</th>';
foreach ($aSupplier as $supplier) {
	echo '<th class="text-center text-white font-weight-bold" colspan="2">' . $supplier['name'] . '</th>';
}
echo '<th class="text-center text-white font-weight-bold" colspan="2">Total</th>';

echo '</tr>';
echo '<tr>';
foreach ($aSupplier as $supplier) {
	echo '<th class="text-center text-white font-weight-bold">KG</th>';
	echo '<th class="text-center text-white font-weight-bold">USD</th>';
}
echo '<th class="text-center text-white font-weight-bold">KG</th>';
echo '<th class="text-center text-white font-weight-bold">USD</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
foreach ($aContent as $key => $content) {
	$eachTotalPurchase = array(0, 0, 0);
	$eachTotal = array(0, 0);
	$isContent = is_array($content);
	echo '<tr>';
	echo '<th class="text-left font-weight">' . ($isContent ? $content[1] : $content) . '</th>';
	foreach ($aSupplier as $key_sup => $supplier) {
		if ($isContent && $content[0] == "supplier") {
			$rst = $dbc->Query(replace_sql_string($content[2], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="text-right ">' . $line[0] . '</td>';
			if (in_array($key, array(0, 1, 2))) {
				$aSum[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(0, 1, 2))) {
				$aSumPurchase[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSum[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSumPending[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotalPurchase[0] += floatval(str_replace(",", "", $line[0]));
			$rst = $dbc->Query(replace_sql_string($content[3], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="text-right">' . number_format((float)$line[0], 2) . '</td>';
			if (in_array($key, array(0, 1, 2))) {
				$aSum[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(0, 1, 2))) {
				$aSumPurchase[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSum[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSumPending[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotalPurchase[1] += floatval(str_replace(",", "", $line[0]));
		} else if ($key == 3) {
			echo '<td class="text-right  text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC);">' . number_format($aSum[$key_sup][0], 4) . '</td>';
			echo '<td class="text-right text-white font-weight-bold" style= "background: linear-gradient(135deg, #00204E, #004080, #0066CC);">' . number_format($aSum[$key_sup][1], 2) . '</td>';
			$aSumPurchase[$key_sup][0] += $aSum[$key_sup][0];
			$aSumPurchase[$key_sup][1] += $aSum[$key_sup][1];
			$eachTotalPurchase[0] += $aSum[$key_sup][0];
			$eachTotalPurchase[1] += $aSum[$key_sup][1];
		} else if ($key == 4) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 5) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 6) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 7) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 8) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 9) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 10) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 11) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 12) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 13) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 14) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 15) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 16) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 17) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 18) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 19) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 20) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 21) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 22) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 23) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 24) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 25) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 26) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 27) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 29) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 30) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 31) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 32) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 33) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 34) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 35) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else {
			echo '<td class="text-center"></td>';
			echo '<td class="text-center">x</td>';
		}
	}
	if ($isContent && $content[0] == "total") {

		if ($key == 63) {
			echo '<td class="text-right text-primary">' . number_format($TotalOnHand, 4) . '</td>';
			echo '<td class="text-center text-danger">' . $key . '</td>';
			$TotalFinal = $TotalOnHand;
		} else if ($key == 53) {
			echo '<td class="text-right text-primary">' . number_format($TotalFinal, 4) . '</td>';
			echo '<td class="text-center text-danger">' . $key . '</td>';
		} else {
			$rst = $dbc->Query(replace_sql_string($content[2]));
			$line = $dbc->Fetch($rst);
			if (is_null($line)) {
				echo '<td class="text-center">-</td>';
			} else if ($key == 12) {
				echo '<th class="text-right bg-primary text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 13) {
				echo '<th class="text-right bg-secondary text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 14) {
				echo '<th class="text-right bg-success text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 15) {
				echo '<th class="text-right bg-success text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 16) {
				echo '<th class="text-right bg-warning text-white font-weight-bold">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 17) {
				echo '<th class="text-right text-white font-weight-bold" style="background-color: #8B4513;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 18) {
				echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 19) {
				echo '<th class="text-right bg-primary text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 20) {
				echo '<th class="text-right bg-secondary text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 21) {
				echo '<th class="text-right bg-success text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 22) {
				echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else if ($key == 23) {
				echo '<th class="text-right text-white font-weight-bold" style="background-color: #9932CC;">' . number_format($line[0], 4) . '</th>';
			} else {
				echo '<th class="text-right font-weight-bold">' . number_format($line[0], 4) . '</th>';
				if (in_array($key, array(19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32))) {
					$TotalOnHand = $TotalOnHand + floatval($line[0]);
				} else if ($key == 62) { // - Total Stock
					$TotalOnHand -= floatval($line[0]);
				} else if ($key == 32) { // - Sigmagin
					$TotalOnHand -= floatval($line[0]);
				} else if ($key == 33) { //R38
					$TotalOnHand += floatval($line[0]);
				} else if ($key == 34) { //R38
					$TotalOnHand += floatval($line[0]);
				} else if ($key == 35) { //Physical
					$TotalOnHand -= floatval($line[0]);
				} else if ($key == 38) { //Fixed Stock
					$TotalOnHand -= floatval($line[0]);
				} else if ($key == 66) { //Fixed Stock
					$TotalFinal += floatval($line[0]);
				} else if ($key == 50) { //Fixed Stock
					$TotalFinal += floatval($line[0]);
				}
			}



			$rst = $dbc->Query(replace_sql_string($content[3]));
			$line = $dbc->Fetch($rst);
			echo '<th class="text-center text-danger font-weight-bold">' . $line[0] . '</th>';
		}
	} else {
		echo '<td class="text-right text-danger font-weight-bold">' . number_format($eachTotalPurchase[0], 4) . '</td>';
		echo '<td class="text-right text-danger font-weight-bold">' . number_format($eachTotalPurchase[1], 2) . '</td>';
		if ($key == 21) {
			$TotalOnHand -= $eachTotalPurchase[0];
		}
	}
	echo '<tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';







$dbc->Close();
