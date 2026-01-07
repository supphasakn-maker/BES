<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../include/const.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$date = strtotime($_POST['date']);
$date_for_sql = $_POST['date'];
$newDate = date("Y-m-d", strtotime("-1 days", strtotime($date_for_sql)));

$order_condition = " bs_orders.parent IS NULL AND bs_orders.status > -1";

// By Pattaragun Junthhomkai
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
$aSumPurchase = array();
$aSumPending = array();
$aSumUntake = array();
$TotalOnHand = 0;
$TotalFinal = 0;
$sql = "SELECT * FROM bs_suppliers_mapping where status =1 AND id NOT IN (28)
ORDER BY FIELD(id,1,6,16,17,18,22,23,24,25,26,20,14,19,21,11)";
$rst = $dbc->Query($sql);
while ($supplier = $dbc->Fetch($rst)) {
	array_push($aSupplier, array(
		"id" => $supplier['id'],
		"name" => $supplier['name'],
		"amount" => $supplier['amount']
	));
	array_push($aSum, array(0, 0));
	array_push($aSumPurchase, array(0, 0));
	array_push($aSumPending, array(0, 0));
	array_push($aSumUntake, array(0, 0));
}

// iPad Pro optimized styling
echo '<style>
body {
    background-color: #ffffff !important;
}

@media screen and (min-width: 768px) and (max-width: 1366px) {
    /* iPad Pro specific styles */
    .report-container {
        padding: 15px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100vw;
        background-color: #ffffff !important;
    }
    
    .report-table {
        min-width: 1400px;
        font-size: 14px;
        border-collapse: collapse;
        margin: 0 auto;
        background-color: #ffffff !important;
    }
    
    .report-table th, 
    .report-table td {
        padding: 8px 6px;
        border: 1px solid #ddd !important;
        white-space: nowrap;
        text-align: center;
        min-width: 80px;
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: linear-gradient(135deg, #00204E, #003366) !important;
        color: #ffffff !important;
        font-weight: bold !important;
    }
    
    .report-table tbody th:first-child {
        position: sticky;
        left: 0;
        z-index: 11;
        background: #f8f9fa !important;
        border-right: 2px solid #00204E !important;
        min-width: 350px;
        max-width: 350px;
        font-size: 14px;
        word-wrap: break-word;
        text-align: left !important;
        color: #000000 !important;
        padding: 8px 10px !important;
        white-space: normal !important;
        line-height: 1.3;
    }
    
    .supplier-header {
        background: linear-gradient(135deg, #00204E, #003366) !important;
        color: #ffffff !important;
        font-weight: bold !important;
        font-size: 11px;
    }
    
    .total-column {
        background: #f0f0f0 !important;
        font-weight: bold !important;
        border-left: 2px solid #00204E !important;
        color: #000000 !important;
    }
    
    .amount-cell {
        font-size: 11px;
        text-align: right !important;
        padding: 6px 4px;
        background-color: #ffffff !important;
        color: #000000 !important;
    }
    
    .bg-dark {
        background-color: #6c757d !important;
        color: #ffffff !important;
    }
    
    .text-primary {
        color: #007bff !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-white {
        color: #ffffff !important;
    }
}

@media screen and (max-width: 767px) {
    /* Mobile fallback */
    .report-container {
        padding: 10px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .report-table {
        font-size: 9px;
        min-width: 800px;
    }
    
    .report-table th, 
    .report-table td {
        padding: 4px 2px;
        min-width: 50px;
    }
}

@media screen and (min-width: 1367px) {
    /* Desktop styles */
    .report-container {
        padding: 20px;
    }
    
    .report-table {
        font-size: 12px;
    }
    
    .report-table th, 
    .report-table td {
        padding: 8px 6px;
    }
}

/* Common responsive utilities */
.text-nowrap { white-space: nowrap; }
.table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>';

echo '<div class="report-container">';
echo '<div class="table-responsive">';
echo '<table class="report-table table table-sm table-bordered">';
echo '<thead>';

echo '<tr>';
echo '<th class="text-nowrap" rowspan="2" style="min-width: 350px; background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">' . $_POST['date'] . '</th>';
foreach ($aSupplier as $supplier) {
	echo '<th class="text-nowrap" colspan="2" style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">' . htmlspecialchars($supplier['name']) . '</th>';
}
echo '<th class="text-nowrap" colspan="2" style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">Total</th>';
echo '</tr>';

echo '<tr>';
foreach ($aSupplier as $supplier) {
	echo '<th style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">KG</th>';
	echo '<th style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">USD</th>';
}
echo '<th style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important; border-left: 2px solid #00204E !important;">KG</th>';
echo '<th style="background: linear-gradient(135deg, #00204E, #003366) !important; color: #ffffff !important; font-weight: bold !important;">USD</th>';
echo '</tr>';
echo '</thead>';

echo '<tbody>';
foreach ($aContent as $key => $content) {
	$eachTotalPurchase = array(0, 0, 0);
	$eachTotal = array(0, 0);
	$isContent = is_array($content);
	echo '<tr>';
	echo '<th class="text-left" style="min-width: 350px; max-width: 350px; word-wrap: break-word; background: #f8f9fa !important; color: #000000 !important; padding: 8px 10px !important; white-space: normal !important; line-height: 1.3;">' .
		htmlspecialchars($isContent ? $content[1] : $content) . '</th>';

	foreach ($aSupplier as $key_sup => $supplier) {
		if ($isContent && $content[0] == "supplier") {
			$rst = $dbc->Query(replace_sql_string($content[2], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="amount-cell">' . number_format((float)$line[0], 4) . '</td>';

			// Update sums (original logic preserved)
			if (in_array($key, array(0, 1, 2))) {
				$aSum[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSum[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
				$aSumPending[$key_sup][0] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotalPurchase[0] += floatval(str_replace(",", "", $line[0]));

			$rst = $dbc->Query(replace_sql_string($content[3], $supplier['id']));
			$line = $dbc->Fetch($rst);
			echo '<td class="amount-cell">' . number_format((float)$line[0], 2) . '</td>';

			// Update sums (original logic preserved)
			if (in_array($key, array(0, 1, 2))) {
				$aSum[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			} else if (in_array($key, array(4, 5, 10, 11, 12))) {
				$aSum[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
				$aSumPending[$key_sup][1] += floatval(str_replace(",", "", $line[0]));
			}
			$eachTotalPurchase[1] += floatval(str_replace(",", "", $line[0]));
		} else if ($key == 3) {
			echo '<td class="amount-cell text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC) !important;">' . number_format($aSum[$key_sup][0], 4) . '</td>';
			echo '<td class="amount-cell text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC) !important;">' . number_format($aSum[$key_sup][1], 2) . '</td>';
			$aSumPurchase[$key_sup][0] += $aSum[$key_sup][0];
			$aSumPurchase[$key_sup][1] += $aSum[$key_sup][1];
			$eachTotalPurchase[0] += $aSum[$key_sup][0];
			$eachTotalPurchase[1] += $aSum[$key_sup][1];
		} else if ($key == 14) {
			echo '<td class="amount-cell text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC) !important;">' . number_format($aSum[$key_sup][0], 4) . '</td>';
			echo '<td class="amount-cell text-white font-weight-bold" style="background: linear-gradient(135deg, #00204E, #004080, #0066CC) !important;">' . number_format($aSum[$key_sup][1], 2) . '</td>';
			$aSumPending[$key_sup][0] += $aSum[$key_sup][0];
			$aSumPending[$key_sup][1] += $aSum[$key_sup][1];
			$eachTotalPurchase[0] += $aSum[$key_sup][0];
			$eachTotalPurchase[1] += $aSum[$key_sup][1];
		} else if (in_array($key, range(15, 53))) {
			echo '<td style="background-color: #6c757d !important; color: #ffffff !important;">-</td>';
			echo '<td style="background-color: #6c757d !important; color: #ffffff !important;">-</td>';
		} else {
			echo '<td class="text-center" style="background-color: #ffffff !important; color: #000000 !important;">-</td>';
			echo '<td class="text-center" style="background-color: #ffffff !important; color: #000000 !important;">-</td>';
		}
	}

	// Total columns handling
	if ($isContent && $content[0] == "total") {
		if ($key == 59) {
			echo '<td class="amount-cell total-column text-primary">' . number_format($TotalOnHand, 4) . '</td>';
			echo '<td class="text-center total-column text-danger">' . $key . '</td>';
			$TotalFinal = $TotalOnHand;
		} else if ($key == 60) {
			echo '<td class="amount-cell total-column text-primary">' . number_format($TotalFinal, 4) . '</td>';
			echo '<td class="text-center total-column text-danger">' . $key . '</td>';
		} else {
			$rst = $dbc->Query(replace_sql_string($content[2]));
			$line = $dbc->Fetch($rst);
			if (is_null($line)) {
				echo '<td class="text-center total-column">-</td>';
			} else {
				// Apply different background colors based on key
				$bgClass = '';
				$textClass = 'text-white font-weight-bold';

				switch ($key) {
					case 23:
						$bgClass = 'bg-primary';
						break;
					case 24:
						$bgClass = 'bg-secondary';
						break;
					case 25:
					case 26:
						$bgClass = 'bg-success';
						break;
					case 27:
						$bgClass = 'bg-warning';
						break;
					case 28:
						$bgClass = 'text-white';
						$textClass .= '" style="background-color: #8B4513;';
						break;
					default:
						if (in_array($key, range(29, 40))) {
							$bgClass = 'text-white';
							$textClass .= '" style="background-color: #9932CC;';
						} else {
							$bgClass = '';
							$textClass = 'font-weight-bold';
						}
				}

				echo '<th class="amount-cell total-column ' . $bgClass . ' ' . $textClass . '">' .
					number_format($line[0], 4) . '</th>';

				// Calculate TotalOnHand (original logic preserved)
				if (in_array($key, array(14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24))) {
					$TotalOnHand += floatval($line[0]);
				} else if ($key == 62) {
					$TotalOnHand -= floatval($line[0]);
				} else if ($key == 32) {
					$TotalOnHand -= floatval($line[0]);
				} else if (in_array($key, array(33, 34))) {
					$TotalOnHand += floatval($line[0]);
				} else if (in_array($key, array(35, 38))) {
					$TotalOnHand -= floatval($line[0]);
				} else if (in_array($key, array(66, 51))) {
					$TotalFinal += floatval($line[0]);
				}
			}

			$rst = $dbc->Query(replace_sql_string($content[3]));
			$line = $dbc->Fetch($rst);
			echo '<th class="text-center total-column text-danger">' . $line[0] . '</th>';
		}
	} else {
		echo '<td class="amount-cell total-column text-danger font-weight-bold">' .
			number_format($eachTotalPurchase[0], 4) . '</td>';
		echo '<td class="amount-cell total-column text-danger font-weight-bold">' .
			number_format($eachTotalPurchase[1], 2) . '</td>';
		if ($key == 21) {
			$TotalOnHand -= $eachTotalPurchase[0];
		}
	}
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

include "loader/load-fx.php";

$dbc->Close();
