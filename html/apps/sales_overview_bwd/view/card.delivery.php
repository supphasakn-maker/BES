<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
    <thead>
        <tr>
            <th class="text-center table-dark font-weight-bold" colspan="10">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y", strtotime($date)); ?></tjh>
        </tr>
        <tr>
            <th class="text-center table-dark font-weight-bold">DATE</th>
            <th class="text-center table-dark font-weight-bold">BILL NO.</th>
            <th class="text-center table-dark font-weight-bold">ORDER NO.</th>
            <th class="text-center table-dark font-weight-bold">CUSTOMER</th>
            <th class="text-center table-dark font-weight-bold">BARS</th>
            <th class="text-center table-dark font-weight-bold">BATH / KGS.</th>
            <th class="text-center table-dark font-weight-bold">TOTAL - DISCOUNT</th>
            <th class="text-center table-dark font-weight-bold">PERIOD</th>
        </tr>
    </thead>
    <!-- /Filter columns -->
    <tbody>
        <?php
        $total_amount = 0;
        $total_net = 0;
        $sql = "SELECT
    ANY_VALUE(parent.date)            AS date,
    ANY_VALUE(d.delivery_date)        AS delivery_date,
    ANY_VALUE(d.billing_id)           AS code,
    ANY_VALUE(parent.customer_name)   AS customer_name,

    COALESCE(SUM(o.amount),   0) AS amount,
    COALESCE(SUM(o.price),    0) AS price,
    COALESCE(SUM(o.total),    0) AS total,
    COALESCE(SUM(o.discount), 0) AS discount,
    COALESCE(SUM(o.net),      0) AS net,

    ANY_VALUE(parent.code)           AS order_number,
    ANY_VALUE(parent.delivery_time)  AS delivery_time

FROM bs_deliveries_bwd AS d
LEFT JOIN bs_orders_bwd AS parent
       ON parent.delivery_id = d.id
      AND parent.parent IS NULL                -- << เอาเฉพาะออเดอร์พ่อ
LEFT JOIN bs_orders_bwd AS o
       ON (o.id = parent.id OR o.parent = parent.id)
      AND o.status > 0                         -- << คัดเฉพาะลูกที่ status > 0
WHERE DATE(d.delivery_date) = '" . $dbc->Escape_String($date) . "'
GROUP BY d.id, parent.id
ORDER BY d.delivery_date ASC;

				";

        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td>' . date("d/m/Y", strtotime($order['date'])) . '</td>';
            echo '<td>' . $order['code'] . '</td>';
            echo '<td>' . $order['order_number'] . '</td>';
            echo '<td>' . $order['customer_name'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td>' . $order['delivery_time'] . '</td>';
            echo '</tr>';
            $total_amount += $order['amount'];
            $total_net += $order['net'];
        }
        ?>

    </tbody>
    <thead>
        <tr>
            <th colspan="4" class="text-right">รวม</th>
            <th class="text-right"><?php echo number_format($total_amount, 4); ?></th>
            <th class="text-center"></th>
            <th class="text-right"><?php echo number_format($total_net, 2); ?></th>
            <th colspan="2"></th>
        </tr>
    </thead>
</table>