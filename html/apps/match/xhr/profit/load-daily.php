<div class="col-xl-12 mb-12">
	<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
		<thead>
			<tr>
				<th class="text-center table-dark" colspan="17">Profit Table</th>
			</tr>
			<tr>
				<th class="text-center" rowspan="2">Date</th>
				<th class="text-center" colspan="3">Sales</th>
				<th class="text-center" colspan="4">Purchase</th>
				<th class="text-center" colspan="3">USD Purchase</th>
				<th class="text-center" colspan="4">Margin</th>
				<th class="text-center" rowspan="2">Profit(THB)</th>
				<th class="text-center" rowspan="2">Trade(THB)</th>
			</tr>
			<tr>
				<th class="text-center">Total Order</th>
				<th class="text-center">Amount (KG)</th>
				<th class="text-center">Total Value (THB)</th>

				<th class="text-center">Total Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">USD Value</th>
				<th class="text-center">THB Value</th>

				<th class="text-center">Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">THB (Purchase USD)</th>

				<th class="text-center">Amount Margin</th>
				<th class="text-center">Amount Balance</th>
				<th class="text-center">USD Margin</th>
				<th class="text-center">USD Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT date 
			FROM (
				(SELECT DATE(mapped) AS date FROM bs_mapping_silvers)
				UNION (SELECT DATE(mapped) AS date FROM bs_mapping_usd)
			) AS tmp
			
			GROUP BY(date) 
			ORDER BY(date) ASC
			;";
			$rst = $dbc->Query($sql);
			$balance_amount = 0;
			$balance_usd = 0;
			while ($record = $dbc->Fetch($rst)) {
				$order = array(0, 0, 0);
				$purchase = array(0, 0, 0, 0);
				$purchase_usd = array(0, 0, 0);
				$margin_amount = 0;
				$margin_usd = 0;
				$trade_value = 0;
				$date_filter = $record['date'];

				$sql = "SELECT
					bs_mapping_silver_orders.amount AS amount,
					bs_orders.price AS price
				FROM bs_mapping_silver_orders
				LEFT JOIN bs_mapping_silvers ON bs_mapping_silver_orders.mapping_id = bs_mapping_silvers.id
				LEFT JOIN bs_orders ON bs_mapping_silver_orders.order_id = bs_orders.id
				WHERE DATE(bs_mapping_silvers.mapped) = '" . $record['date'] . "'";
				$rst_order = $dbc->Query($sql);
				while ($item = $dbc->Fetch($rst_order)) {
					$order[0] += 1;
					$order[1] += $item['amount'];
					$order[2] += $item['price'] * $item['amount'];
					$margin_amount -= $item['amount'];
				}

				$sql = "SELECT
					bs_mapping_silver_purchases.amount AS amount,
					bs_purchase_spot.rate_spot AS rate_spot,
					bs_purchase_spot.rate_pmdc AS rate_pmdc
				FROM bs_mapping_silver_purchases
				LEFT JOIN bs_mapping_silvers ON bs_mapping_silver_purchases.mapping_id = bs_mapping_silvers.id
				LEFT JOIN bs_purchase_spot ON bs_mapping_silver_purchases.purchase_id = bs_purchase_spot.id
				WHERE DATE(bs_mapping_silvers.mapped) = '" . $record['date'] . "' AND bs_purchase_spot.currency = 'USD'";

				$rst_purchase = $dbc->Query($sql);
				while ($item = $dbc->Fetch($rst_purchase)) {
					$total = ($item['rate_spot'] + $item['rate_pmdc']) * $item['amount'] * 32.1507;
					$purchase[0] += 1;
					$purchase[1] += $item['amount'];
					$purchase[2] += $total;
					$margin_amount += $item['amount'];
					$margin_usd -= $total;
				}

				$sql = "SELECT
					bs_mapping_silver_purchases.amount AS amount,
					bs_purchase_spot.THBValue AS THBValue,
					bs_purchase_spot.amount AS am
				FROM bs_mapping_silver_purchases
				LEFT JOIN bs_mapping_silvers ON bs_mapping_silver_purchases.mapping_id = bs_mapping_silvers.id
				LEFT JOIN bs_purchase_spot ON bs_mapping_silver_purchases.purchase_id = bs_purchase_spot.id
				WHERE DATE(bs_mapping_silvers.mapped) = '" . $record['date'] . "' AND bs_purchase_spot.currency = 'THB'";

				$rst_purchase = $dbc->Query($sql);
				while ($item = $dbc->Fetch($rst_purchase)) {
					$purchase[1] += $item['amount'];
					$purchase[3] += ($item['amount'] * $item['THBValue'] / $item['am']);
					$margin_amount += $item['amount'];
				}

				$sql = "SELECT
					bs_mapping_usd_purchases.amount AS amount,
					bs_purchase_usd.rate_finance AS rate_finance
				FROM bs_mapping_usd_purchases
				LEFT JOIN bs_mapping_usd ON bs_mapping_usd_purchases.mapping_id = bs_mapping_usd.id
				LEFT JOIN bs_purchase_usd ON bs_mapping_usd_purchases.purchase_id = bs_purchase_usd.id
				WHERE DATE(bs_mapping_usd.mapped) = '" . $record['date'] . "'";
				$rst_purchase = $dbc->Query($sql);
				while ($item = $dbc->Fetch($rst_purchase)) {
					$purchase_usd[0] += 1;
					$purchase_usd[1] += $item['amount'];
					$purchase_usd[2] += $item['amount'] * $item['rate_finance'];
					$margin_usd += $item['amount'];
				}

				// คำนวณ Trade Value
				try {
					$sql_trade = "SELECT 
						(SELECT COALESCE(SUM(amount*rate_spot*32.1507), 0) FROM bs_sales_spot WHERE trade_id = bs_trade_spot.id) as total_sales,
						(SELECT COALESCE(SUM(amount*rate_spot*32.1507), 0) FROM bs_purchase_spot WHERE trade_id = bs_trade_spot.id) as total_purchase
					FROM bs_trade_spot 
					WHERE bs_trade_spot.date = '$date_filter'";

					$rst_trade = $dbc->Query($sql_trade);
					$trade_diff_usd = 0;

					if ($rst_trade) {
						while ($item_trade = $dbc->Fetch($rst_trade)) {
							$sales = floatval($item_trade['total_sales'] ?? 0);
							$purchase_trade = floatval($item_trade['total_purchase'] ?? 0);
							$trade_diff_usd += ($sales - $purchase_trade);
						}
					}

					// Get rate_exchange for the date
					$rate_exchange = 1;
					$sql_rate = "SELECT rate_exchange FROM bs_purchase_usd 
								WHERE DATE(created) = '$date_filter' 
								ORDER BY created DESC LIMIT 1";
					$rst_rate = $dbc->Query($sql_rate);
					if ($rst_rate && ($rate_item = $dbc->Fetch($rst_rate))) {
						$rate_exchange = floatval($rate_item['rate_exchange'] ?? 1);
					} else {
						$sql_rate = "SELECT rate_exchange FROM bs_purchase_usd 
									WHERE DATE(created) < '$date_filter' 
									ORDER BY created DESC LIMIT 1";
						$rst_rate = $dbc->Query($sql_rate);
						if ($rst_rate && ($rate_item = $dbc->Fetch($rst_rate))) {
							$rate_exchange = floatval($rate_item['rate_exchange'] ?? 1);
						}
					}

					$trade_value = $trade_diff_usd * $rate_exchange;
				} catch (Exception $e) {
					$trade_value = 0;
				}

				$balance_amount += $margin_amount;
				$balance_usd += $margin_usd;

				echo '<tr>';

				echo '<td class="text-center">' . $record['date'] . '</td>';

				echo '<td class="text-center">' . number_format($order[0], 0) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($order[1], 4) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($order[2], 2) . '</td>';

				echo '<td class="text-center">' . number_format($purchase[0], 0) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($purchase[1], 4) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($purchase[2], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($purchase[3], 2) . '</td>';

				echo '<td class="text-center">' . number_format($purchase_usd[0], 0) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($purchase_usd[1], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($purchase_usd[2], 2) . '</td>';

				$margin_class = ($margin_amount > 0) ? " text-success" : " text-danger";
				echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_amount, 4) . '</td>';

				echo '<td class="text-right pr-2">' . number_format($balance_amount, 4) . '</td>';

				$margin_class = ($margin_usd > 0) ? " text-success" : " text-danger";
				echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_usd, 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($balance_usd, 2) . '</td>';

				$profit = $order[2] - $purchase_usd[2] - $purchase[3];
				$profit_class = ($profit > 0) ? " text-success" : " text-danger";
				echo '<td class="text-right pr-2' . $profit_class . '">' . number_format($profit, 2) . '</td>';

				$trade_class = ($trade_value > 0) ? " text-success" : (($trade_value < 0) ? " text-danger" : "");
				echo '<td class="text-right pr-2' . $trade_class . '">' . number_format($trade_value, 2) . '</td>';

				echo '</tr>';
			}
			?>

		</tbody>
		<thead>

			<tr>
				<th class="text-center" rowspan="2">Date</th>
				<th class="text-center" colspan="3">Sales</th>
				<th class="text-center" colspan="4">Purchase</th>
				<th class="text-center" colspan="3">USD Purchase</th>
				<th class="text-center" colspan="4">Margin</th>
				<th class="text-center" rowspan="2">Profit(THB)</th>
				<th class="text-center" rowspan="2">Trade(THB)</th>
			</tr>
			<tr>
				<th class="text-center">Total Order</th>
				<th class="text-center">Amount (KG)</th>
				<th class="text-center">Total Value (THB)</th>

				<th class="text-center">Total Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">USD Value</th>
				<th class="text-center">THB Value</th>

				<th class="text-center">Purchase</th>
				<th class="text-center">Amount</th>
				<th class="text-center">THB (Purchase USD)</th>

				<th class="text-center">Amount Margin</th>
				<th class="text-center">Amount Balance</th>
				<th class="text-center">USD Margin</th>
				<th class="text-center">USD Balance</th>
			</tr>
		</thead>
	</table>
</div>