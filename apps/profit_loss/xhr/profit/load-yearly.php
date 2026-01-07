<div class="col-xl-12 mb-12">
    <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
        <thead>
            <tr>
                <th class="text-center table-dark" colspan="16">รายปี</th>
            </tr>
            <tr>
                <th class="text-center" rowspan="2">Year</th>
                <th class="text-center" colspan="3">Sales</th>
                <th class="text-center" colspan="4">Purchase</th>
                <th class="text-center" colspan="3">USD Purchase</th>
                <th class="text-center" colspan="4">Margin</th>
                <th class="text-center" rowspan="2">Profit(THB)</th>
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
            $sql = "SELECT 
				YEAR(date) AS y
			FROM (
				(SELECT DATE(mapped) AS date FROM bs_mapping_silvers)
				UNION (SELECT DATE(mapped) AS date FROM bs_mapping_usd)
			) AS tmp
			
			GROUP BY(y) 
			ORDER BY(y) ASC
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
                $sql = "SELECT
					bs_mapping_silver_orders.amount AS amount,
					bs_orders.price AS price
				FROM bs_mapping_silver_orders
				LEFT JOIN bs_mapping_silvers ON bs_mapping_silver_orders.mapping_id = bs_mapping_silvers.id
				LEFT JOIN bs_orders ON bs_mapping_silver_orders.order_id = bs_orders.id
				WHERE YEAR(bs_mapping_silvers.mapped) = '" . $record[0] . "'";

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
				WHERE YEAR(bs_mapping_silvers.mapped) = '" . $record[0] . "'
					AND bs_purchase_spot.currency = 'USD'";

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
				WHERE YEAR(bs_mapping_silvers.mapped) = '" . $record[0] . "' 
					AND bs_purchase_spot.currency = 'THB'";

                $rst_purchase = $dbc->Query($sql);
                while ($item = $dbc->Fetch($rst_purchase)) {
                    $purchase[1] += $item['amount'];
                    $purchase[3] += ($item['amount'] * $item['THBValue'] / $item['am']);
                    $margin_amount += $item['amount'];
                }


                $sql = "SELECT
					bs_mapping_usd_purchases.amount AS amount,
					bs_purchase_usd.rate_exchange AS rate_exchange
				FROM bs_mapping_usd_purchases
				LEFT JOIN bs_mapping_usd ON bs_mapping_usd_purchases.mapping_id = bs_mapping_usd.id
				LEFT JOIN bs_purchase_usd ON bs_mapping_usd_purchases.purchase_id = bs_purchase_usd.id
				WHERE YEAR(bs_mapping_usd.mapped) = '" . $record[0] . "'";

                $rst_purchase = $dbc->Query($sql);
                while ($item = $dbc->Fetch($rst_purchase)) {
                    $purchase_usd[0] += 1;
                    $purchase_usd[1] += $item['amount'];
                    $purchase_usd[2] += $item['amount'] * $item['rate_exchange'];
                    $margin_usd += $item['amount'];
                }

                $balance_amount += $margin_amount;
                $balance_usd += $margin_usd;


                echo '<tr>';

                $title = $record[0];
                echo '<td class="text-center" >' . $title . '</td>';


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
                echo '<td class="text-right pr-2">' . number_format($order[2] - $purchase_usd[2] - $purchase[3], 2) . '</td>';


                echo '</tr>';
            }
            ?>

        </tbody>
        <thead>

            <tr>
                <th class="text-center" rowspan="2">Year</th>
                <th class="text-center" colspan="3">Sales</th>
                <th class="text-center" colspan="4">Purchase</th>
                <th class="text-center" colspan="3">USD Purchase</th>
                <th class="text-center" colspan="4">Margin</th>
                <th class="text-center" rowspan="2">Profit(THB)</th>
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