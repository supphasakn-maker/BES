<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead>
        <tr>
            <th class="text-center table-dark font-weight-bold" colspan="10">รายงานแยกตามวันส่ง</th>
        </tr>
        <tr>
            <th class="text-center table-dark font-weight-bold">DELIVERY DATE</th>
            <th class="text-center table-dark font-weight-bold">BARS</th>
            <th class="text-center table-dark font-weight-bold">TOTAL</th>
            <th class="text-center table-dark font-weight-bold">TOTAL - DISCOUNT</th>
        </tr>
    </thead>
    <!-- /Filter columns -->
    <tbody>
        <?php
        $sql = "SELECT SUM(amount) AS amount , SUM(price) AS price FROM bs_orders_bwd WHERE delivery_date IS NULL AND date > '2023-12-31' AND status > 0";
        $rst = $dbc->Query($sql);
        $balance_delivery = $dbc->Fetch($rst);
        $total_amount1 =  $balance_delivery["amount"];
        $total_price1 = $balance_delivery["price"];
        $locksum = $total_amount * $total_price1 * 0.07;

        $date;
        $datetomorrow = date('Y-m-d', strtotime($date . "+1 days"));
        $total_amount = 0;
        $total_total = 0;
        $total_net = 0;
        $sql = "SELECT 
                d.delivery_date AS delivery_date,
                COUNT(d.id) AS id,
                SUM(o.amount) AS amount,
                SUM(o.total) AS total,
                SUM(o.net) AS net
            FROM bs_deliveries_bwd d
            LEFT JOIN bs_orders_bwd parent ON parent.delivery_id = d.id
            LEFT JOIN bs_orders_bwd o ON (
                o.id = parent.id OR o.parent = parent.id
            )
            WHERE o.status > 0 
                AND d.delivery_date >= '" . $datetomorrow . "' 
            GROUP BY d.delivery_date
            ORDER BY d.delivery_date ASC
				";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . date("d/m/Y", strtotime($order['delivery_date'])) . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';
            $total_amount += $order['amount'];
            $total_total += $order['total'];
            $total_net += $order['net'];
        }
        $total_amount += $total_amount1;
        $total_total += $total_price1;
        $total_net += $locksum;

        ?>

    </tbody>
    <thead>
        <tr>
            <th class="text-right">Lock</th>
            <th class="text-right"><?php echo number_format($total_amount1, 4); ?></th>
            <th class="text-right"><?php echo number_format($total_price1, 2); ?></th>

            <th class="text-right"><?php echo number_format($locksum, 2); ?></th>

        </tr>
    </thead>
    <thead>
        <tr>
            <th class="text-right">รวม</th>
            <th class="text-right"><?php echo number_format($total_amount, 4); ?></th>
            <th class="text-right"><?php echo number_format($total_total, 2); ?></th>

            <th class="text-right"><?php echo number_format($total_net, 2); ?></th>

        </tr>
    </thead>

</table>