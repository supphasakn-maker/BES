<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../include/const.php";

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



function replace_sql_string($text, $supplier_id = 0)
{
	$text = str_replace("#sql_date#", $_POST['date'], $text);
	$text = str_replace("#sql_date2#", date("Y-m-d", strtotime("-1 days", strtotime($_POST['date']))), $text);
	$text = str_replace("#supplier_id#", $supplier_id, $text);
	return $text;
}

$aSupplier = array();
$aSum = array();
$aSumPending = array();
$aSumUntake = array();
$TotalOnHand = 0;
$TotalFinal = 0;
$sql = "SELECT * FROM bs_suppliers_mapping where status =1";
$rst = $dbc->Query($sql);
while ($supplier = $dbc->Fetch($rst)) {
	array_push($aSupplier, array(
		"id" => $supplier['id'],
		"name" => $supplier['name'],
		"amount" => $supplier['amount']
	));
	array_push($aSum, array(0, 0));
	array_push($aSumPending, array(0, 0));
	array_push($aSumUntake, array(0, 0));
}
echo '<div class="mb-3">';
echo '<table class="table table-sm table-bordered">';
echo '<thead class="bg-dark">';
echo '<tr>';
echo '<th class="text-center text-white" rowspan="2">' . $_POST['date'] . '</th>';
foreach ($aSupplier as $supplier) {
	echo '<th class="text-center text-white" colspan="2">' . $supplier['name'] . '</th>';
}
echo '<th class="text-center text-white" colspan="2">Total</th>';

echo '</tr>';
echo '<tr>';
foreach ($aSupplier as $supplier) {
	echo '<th class="text-center text-white">KG</th>';
	echo '<th class="text-center text-white">USD</th>';
}
echo '<th class="text-center text-white">KG</th>';
echo '<th class="text-center text-white">USD</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
foreach ($aContent as $key => $content) {
	$eachTotal = array(0, 0);
	$isContent = is_array($content);
	echo '<tr>';
	echo '<th class="text-left font-weight">' . ($isContent ? $content[1] : $content) . '</th>';
	foreach ($aSupplier as $key_sup => $supplier) {
		if ($isContent && $content[0] == "supplier") {
			$rst = $dbc->Query(replace_sql_string($content[2], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="text-right ">' . $line[0] . '</td>';
			if (in_array($key, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9))) {
				$aSum[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(11, 12, 13, 14, 15))) {
				$aSumPending[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(17, 18, 19, 20))) {
				$aSumUntake[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotal[0] += floatval(str_replace(",", "", $line[0]));

			$rst = $dbc->Query(replace_sql_string($content[3], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="text-right">' . number_format((float)$line[0], 4) . '</td>';
			if (in_array($key, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9))) {
				$aSum[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(11, 12, 13, 14, 15))) {
				$aSumPending[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(17, 18, 19, 20))) {
				$aSumUntake[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotal[1] += floatval(str_replace(",", "", $line[0]));
		} else if ($key == 2) {
			echo '<td class="text-right">' . number_format($aSum[$key_sup][0], 4) . '</td>';
			echo '<td class="text-right">' . number_format($aSum[$key_sup][1], 2) . '</td>';
			$aSumPending[$key_sup][0] += $aSum[$key_sup][0];
			$aSumPending[$key_sup][1] += $aSum[$key_sup][1];
			$eachTotal[0] += $aSum[$key_sup][0];
			$eachTotal[1] += $aSum[$key_sup][1];
		} else if ($key == 5) {
			echo '<td class="text-right bg-primary text-white">' . number_format($aSum[$key_sup][0], 4) . '</td>';
			echo '<td class="text-right bg-primary text-white">' . number_format($aSum[$key_sup][1], 4) . '</td>';
			$aSumPending[$key_sup][0] += $aSum[$key_sup][0];
			$aSumPending[$key_sup][1] += $aSum[$key_sup][1];
			$eachTotal[0] += $aSum[$key_sup][0];
			$eachTotal[1] += $aSum[$key_sup][1];
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
		}else if ($key == 13) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 14) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 15) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 16) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 17) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 18) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 19) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 20) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		} else if ($key == 21) {
			echo '<td class="text-right">' . number_format($aSumUntake[$key_sup][0], 4) . '</td>';
			echo '<td class="text-right">' . number_format($aSumUntake[$key_sup][1], 2) . '</td>';
			//$aSumUntake[$key_sup][0] += $aSumPending[$key_sup][0];
			//$aSumUntake[$key_sup][1] += $aSumPending[$key_sup][1];
			$eachTotal[0] += $aSumUntake[$key_sup][0];
			$eachTotal[1] += $aSumUntake[$key_sup][1];
		}else if ($key == 22) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 23) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 24) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 25) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 26) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 27) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 28) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 29) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 32) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 33) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 34) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 35) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 36) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 37) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 38) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 39) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 40) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 41) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 42) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 43) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 44) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 45) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 46) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 47) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 48) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 49) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 50) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 51) {
			echo '<td class="bg-dark"></td>';
			echo '<td class="bg-dark"></td>';
		}else if ($key == 52) {
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
			} else {
				echo '<th class="text-right">' . number_format($line[0], 4) . '</th>';
				if (in_array($key, array(19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31))) {
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
			echo '<th class="text-center text-danger">' . $line[0] . '</th>';
		}
	} else {
		echo '<td class="text-right text-danger">' . number_format(-$eachTotal[0], 4) . '</td>';
		echo '<td class="text-right text-danger">' . number_format(-$eachTotal[1], 2) . '</td>';
		if ($key == 21) {
			$TotalOnHand -= $eachTotal[0];
		}
	}
	echo '<tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';


echo '<div class="mb-3  d-none">';
echo '<table class="table table-sm table-bordered">';
echo '<tbody>';
echo '<tr>';
echo '<th class="text-left">Total USD used</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Total decrease in forward contracts</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Checking</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Total Reservation</th>';
echo '<th class="text-right">Quantity (Kgs)</th>';
echo '<th class="text-right">No. of lot</th>';
echo '</tr>';

echo '<tr>';
echo '<th class="text-left">Mitsui</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">WF</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Standard</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Scotia</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">INTL</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Samsung</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-right">Fx can be used</th>';
echo '<th class="text-right"></th>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">SCB</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">BBL</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Kbank</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Total</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-right">FX Required </th>';
echo '<th class="text-right"></th>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">All Suppliers</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Spot physical position</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Total FX Required</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Balance FX</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Stock</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Actual Balance FX</th>';
echo '<td class="text-right"></td>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div class="mb-3  d-none">';
echo '<table class="table table-sm table-bordered bg-warning">';
echo '<thead>';
echo '<tr>';
echo '<th class="text-center"></th>';
echo '<th class="text-center"></th>';
echo '<th class="text-center">B/F</th>';
echo '<th class="text-center">LC</th>';
echo '<th class="text-center">Interest</th>';
echo '<th class="text-center">Charges</th>';
echo '<th class="text-center">Gain (Loss) From Trade</th>';
echo '<th class="text-center">Deposit</th>';
echo '<th class="text-center">Add</th>';
echo '<th class="text-center"Used Today</th>';
echo '<th class="text-center">Others</th>';
echo '<th class="text-center">Deduct Deferred Stock Mkt Value</th>';
echo '<th class="text-center">C/F</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
echo '<tr>';
echo '<th class="text-left">Require FX before deduct deposit</th>';
echo '<th class="text-left"></th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Outstanding USD / Deposit</th>';
echo '<th class="text-left"></th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">WF</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">INTL (Physical) </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">Scotia</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">Local </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">MKS HONGKONG </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">Samsung</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">STD (STBLC) </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">STD (Physical) (เงินเกินที่ STD.)</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">STD (Fund in) (Defer) </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">STD (Trade) </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">STD (Adjust Cost) </th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left"></th>';
echo '<th class="text-left">SPC</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Require FX after deduct deposit</th>';
echo '<th class="text-left"></th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Open Non Fix</th>';
echo '<th class="text-left">SCB</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Open Non Fix</th>';
echo '<th class="text-left">KBANK</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Open Non Fix</th>';
echo '<th class="text-left">BBL</th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '<tr>';
echo '<th class="text-left">Total require FX amount</th>';
echo '<th class="text-left"></th>';
echo '<td class="text-right"></td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

include "loader/load-fx3.php";
include "loader/load-fx.php";
include "loader/load-fx2.php";






$dbc->Close();
