<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$balance = -4710.36;
if ($_POST['type'] == "daily") {
?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">Monthly</th>
					<th class="text-center" colspan="3">Purchase Spot</th>
					<th class="text-center" colspan="3">Purchase USD</th>
					<th class="text-center" colspan="2">Summary</th>

				</tr>
				<tr>
					<th class="text-center">Total Purchase</th>
					<th class="text-center">Amount</th>
					<th class="text-center">USD Value</th>

					<th class="text-center">Total Purchase</th>
					<th class="text-center">USD Amount</th>
					<th class="text-center">THB Amount</th>

					<th class="text-center">Margin</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>

			<body>
				<?php

				$sql = "SELECT date
			FROM ((SELECT DATE(date) AS date FROM bs_purchase_usd) 
			UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
			WHERE YEAR(date) > 2021 
			
			";
				if ($_POST['month'] != "") {
					$time_filter = strtotime($_POST['month']);
					$sql .= " AND date >= '" . date("Y-m-d", $time_filter) . "'";

					$sql_usd = "SELECT SUM(amount) FROM bs_purchase_usd WHERE YEAR(date) > 2021 AND date < '" . date("Y-m-d", $time_filter) . "' AND bs_purchase_usd.parent IS NULL AND status > -1 AND (bs_purchase_usd.type LIKE 'physical')";
					$rst = $dbc->Query($sql_usd);
					$item = $dbc->Fetch($rst);
					$balance += $item[0];

					$sql_spot = "SELECT SUM(amount*32.1507*(rate_spot+rate_pmdc)) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '" . date("Y-m-d", $time_filter) . "' AND (bs_purchase_spot.type LIKE 'Physical') AND parent IS NULL AND rate_spot > 0";
					$rst = $dbc->Query($sql_spot);
					$item = $dbc->Fetch($rst);
					$balance -= $item[0];
				}
				$sql .= "GROUP BY(date);";

				echo '<tr>';
				echo '<td class="text-center">Start Balance</td>';
				echo '<td colspan=5">';
				echo '<td class="text-center" ></td>';
				echo '<td class="text-center" >' . $balance . '</td>';
				echo '</tr>';
				$rst = $dbc->Query($sql);
				while ($record = $dbc->Fetch($rst)) {
					$spot = array();
					$purchase = array();
					$margin = 0;
					$sql = "SELECT COUNT(id),SUM(amount),SUM(amount*(rate_pmdc+rate_spot)*32.1507) 
				FROM bs_purchase_spot WHERE  DATE(date) = '" . $record['date'] . "'
				AND (bs_purchase_spot.type LIKE 'Physical') AND parent IS NULL AND rate_spot > 0 AND confirm IS NOT NULL";
					$rst_spot = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_spot)) {
						array_push($spot, array(
							$item[0],
							$item[1],
							$item[2]
						));
						$margin -= $item[2];
					}

					$sql = "SELECT COUNT(id),SUM(amount),SUM(amount*rate_exchange)  FROM bs_purchase_usd 
				WHERE  DATE(date) = '" . $record['date'] . "'
				AND (bs_purchase_usd.type LIKE 'Physical') AND parent IS NULL  AND confirm IS NOT NULL";
					$rst_purchase = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_purchase)) {
						array_push($purchase, array(
							$item[0],
							$item[1],
							$item[2]
						));
						$margin += $item[1];
					}

					$balance += $margin;


					$total_row = 1;
					if (count($spot) > $total_row) $total_row = count($spot);
					if (count($purchase) > $total_row) $total_row = count($purchase);

					for ($i = 0; $i < $total_row; $i++) {
						echo '<tr>';
						if ($i == 0) {
							$title = $record['date'];
							echo '<td class="text-center" rowspan="' . $total_row . '">' . $title . '</td>';
						}
						if (isset($spot[$i])) {
							echo '<td class="text-center">' . number_format($spot[$i][0], 0) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($spot[$i][1], 4) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($spot[$i][2], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}

						if (isset($purchase[$i])) {
							echo '<td class="text-center">' . number_format($purchase[$i][0], 0) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($purchase[$i][1], 2) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($purchase[$i][2], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}

						if ($i == 0) {
							echo '<td class="text-right pr-2" rowspan="' . $total_row . '">' . number_format($margin, 4) . '</td>';
							echo '<td class="text-right pr-2" rowspan="' . $total_row . '">' . number_format($balance, 4) . '</td>';
						}
						echo '</tr>';
					}
				}
				?>
			</body>
		</table>
	</div>
<?php
} else if ($_POST['type'] == "monthly") {
?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center" rowspan="2">Monthly</th>
					<th class="text-center" colspan="3">Purchase Spot</th>
					<th class="text-center" colspan="3">Purchase USD</th>
					<th class="text-center" colspan="2">Summary</th>

				</tr>
				<tr>
					<th class="text-center">Total Purchase</th>
					<th class="text-center">Amount</th>
					<th class="text-center">USD Value</th>

					<th class="text-center">Total Purchase</th>
					<th class="text-center">USD Amount</th>
					<th class="text-center">THB Value</th>

					<th class="text-center">Margin</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>

			<body>
				<?php


				$sql = "SELECT YEAR(date),MONTH(date)
			FROM ((SELECT DATE(date) AS date FROM bs_purchase_usd) 
			UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp 
			WHERE YEAR(date) > 2021 
			
			";
				if ($_POST['month'] != "") {
					$time_filter = strtotime($_POST['month']);
					$sql .= " AND date >= '" . date("Y-m-d", $time_filter) . "'";

					$sql_usd = "SELECT SUM(amount) FROM bs_purchase_usd WHERE YEAR(date) > 2021 AND date < '" . date("Y-m-d", $time_filter) . "' AND (bs_purchase_usd.type LIKE 'Physical') AND bs_purchase_usd.parent IS NULL AND status > -1";
					$rst = $dbc->Query($sql_usd);
					$item = $dbc->Fetch($rst);
					$balance += $item[0];

					$sql_spot = "SELECT SUM(amount*32.1507*(rate_spot+rate_pmdc)) FROM bs_purchase_spot WHERE YEAR(date) > 2021 AND date < '" . date("Y-m-d", $time_filter) . "' AND (bs_purchase_spot.type LIKE 'Physical') AND parent IS NULL AND rate_spot > 0";
					$rst = $dbc->Query($sql_spot);
					$item = $dbc->Fetch($rst);
					$balance -= $item[0];
				}
				$sql .= "GROUP BY YEAR(date),MONTH(date);";

				echo '<tr>';
				echo '<td class="text-center">Start Balance</td>';
				echo '<td colspan=5">';
				echo '<td class="text-center" ></td>';
				echo '<td class="text-center" >' . $balance . '</td>';
				echo '</tr>';
				$rst = $dbc->Query($sql);
				while ($record = $dbc->Fetch($rst)) {
					$spot = array();
					$purchase = array();
					$margin = 0;
					$sql = "SELECT COUNT(id),SUM(amount),SUM(amount*(rate_pmdc+rate_spot)*32.1507) 
				FROM bs_purchase_spot WHERE YEAR(date) = '" . $record[0] . "' AND MONTH(date) = '" . $record[1] . "' 
				AND (bs_purchase_spot.type LIKE 'Physical') AND parent IS NULL AND rate_spot > 0 AND confirm IS NOT NULL";
					$rst_spot = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_spot)) {
						array_push($spot, array(
							$item[0],
							$item[1],
							$item[2]
						));
						$margin -= $item[2];
					}

					$sql = "SELECT COUNT(id),SUM(amount),SUM(amount*rate_exchange)  FROM bs_purchase_usd 
				WHERE YEAR(date) = '" . $record[0] . "' AND MONTH(date) = '" . $record[1] . "'
				AND (bs_purchase_usd.type LIKE 'Physical') AND parent IS NULL AND confirm IS NOT NULL";
					$rst_purchase = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_purchase)) {
						array_push($purchase, array(
							$item[0],
							$item[1],
							$item[2]
						));
						$margin += $item[1];
					}

					$balance += $margin;


					$total_row = 1;
					if (count($spot) > $total_row) $total_row = count($spot);
					if (count($purchase) > $total_row) $total_row = count($purchase);

					for ($i = 0; $i < $total_row; $i++) {
						echo '<tr>';
						if ($i == 0) {
							$title = $record[1] . "/" . $record[0];
							echo '<td class="text-center" rowspan="' . $total_row . '">' . $title . '</td>';
						}
						if (isset($spot[$i])) {
							echo '<td class="text-center">' . number_format($spot[$i][0], 0) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($spot[$i][1], 4) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($spot[$i][2], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}

						if (isset($purchase[$i])) {
							echo '<td class="text-center">' . number_format($purchase[$i][0], 0) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($purchase[$i][1], 2) . '</td>';
							echo '<td class="text-right pr-2">' . number_format($purchase[$i][2], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}

						if ($i == 0) {
							echo '<td class="text-right pr-2" rowspan="' . $total_row . '">' . number_format($margin, 4) . '</td>';
							echo '<td class="text-right pr-2" rowspan="' . $total_row . '">' . number_format($balance, 4) . '</td>';
						}
						echo '</tr>';
					}
				}
				?>
			</body>
		</table>
	</div>
<?php
} else {
?>
	<div class="col-xl-12 mb-12">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center table-dark" colspan="11">Date</th>
				</tr>
				<tr>
					<th class="text-center" rowspan="2">Date</th>
					<th class="text-center" colspan="4">Purchase Silver Spot</th>
					<th class="text-center" colspan="4">Pruchase USD</th>
					<th class="text-center" colspan="2">Margin</th>
				</tr>
				<tr>
					<th class="text-center">Amount</th>
					<th class="text-center">Rate Spot</th>
					<th class="text-center">Rate Pmdc</th>
					<th class="text-center">Total (USD)</th>

					<th class="text-center">Bank</th>
					<th class="text-center">Amount</th>
					<th class="text-center">Exchange Rate</th>
					<th class="text-center">Total</th>

					<th class="text-center">Profit (+buy_spot,-buy_usd)</th>
					<th class="text-center">Balance</th>
				</tr>
			</thead>

			<body>
				<?php
				$sql = "SELECT date FROM ((SELECT DATE(date) AS date FROM bs_purchase_usd) UNION (SELECT date AS date FROM bs_purchase_spot)) AS tmp WHERE YEAR(date) > 2021 GROUP BY(date) ORDER BY (date);";
				$rst = $dbc->Query($sql);
				$balance = -4710.36;
				while ($record = $dbc->Fetch($rst)) {
					$purchase_spot = array();
					$purchase = array();
					$margin = 0;
					$sql = "SELECT * FROM bs_purchase_spot WHERE DATE(date) = '" . $record['date'] . "'
						AND bs_purchase_spot.currency != 'THB' 
						AND (bs_purchase_spot.type LIKE 'Physical') AND bs_purchase_spot.rate_spot > 0 AND status > 0 AND confirm IS NOT NULL
					";
					$rst_purchase_spot = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_purchase_spot)) {
						if (!$dbc->HasRecord("bs_purchase_spot", "parent=" . $item['id'])) {
							$total = $item['amount'] * ($item['rate_spot'] + $item['rate_pmdc']) * 32.1507;
							array_push($purchase_spot, array(
								$item['amount'],
								$item['rate_spot'],
								$item['rate_pmdc'],
								$total
							));
							$margin -= $total;
						}
					}

					$sql = "SELECT * FROM bs_purchase_usd
					WHERE date = '" . $record['date'] . "' AND (bs_purchase_usd.type LIKE 'Physical') AND parent IS NULL AND confirm IS NOT NULL";
					$rst_purchase = $dbc->Query($sql);
					while ($item = $dbc->Fetch($rst_purchase)) {
						//$total =($item['rate_spot']+$item['rate_pmdc'])*$item['amount'];
						//if(!$dbc->HasRecord("bs_purchase_usd","parent=".$item['id'])){
						array_push($purchase, array(
							$item['bank'],
							$item['amount'],
							$item['rate_finance'],
							$item['rate_finance'] * $item['amount']
						));
						$margin += $item['amount'];
						//}
					}

					$balance += $margin;


					$total_row = 1;
					if (count($purchase_spot) > $total_row) $total_row = count($purchase_spot);
					if (count($purchase) > $total_row) $total_row = count($purchase);

					for ($i = 0; $i < $total_row; $i++) {
						echo '<tr>';
						if ($i == 0) {
							echo '<td class="text-center" rowspan="' . $total_row . '">' . $record['date'] . '</td>';
						}

						if (isset($purchase_spot[$i])) {
							echo '<td class="text-right">' . number_format($purchase_spot[$i][0], 4) . '</td>';
							echo '<td class="text-right">' . number_format($purchase_spot[$i][1], 4) . '</td>';
							echo '<td class="text-right">' . number_format($purchase_spot[$i][2], 4) . '</td>';
							echo '<td class="text-right">' . number_format($purchase_spot[$i][3], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}


						if (isset($purchase[$i])) {
							echo '<td class="text-center">' . $purchase[$i][0] . '</td>';
							echo '<td class="text-right">' . number_format($purchase[$i][1], 2) . '</td>';
							echo '<td class="text-right">' . number_format($purchase[$i][2], 4) . '</td>';
							echo '<td class="text-right">' . number_format($purchase[$i][3], 2) . '</td>';
						} else {
							echo '<td colspan="4">';
						}



						if ($i == 0) {
							echo '<td class="text-right" rowspan="' . $total_row . '">' . number_format($margin, 2) . '</td>';
							echo '<td class="text-right" rowspan="' . $total_row . '">' . number_format($balance, 2) . '</td>';
						}
						echo '</tr>';
					}
				}
				?>
			</body>
		</table>
	</div>
<?php
}

$dbc->Close();
?>