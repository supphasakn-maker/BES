<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <th class="text-center text-white">DATE ADD</th>
            <th class="text-center text-white">ORDER NO.</th>
            <th class="text-center text-white">CUSTOMER</th>
            <th class="text-center text-white">KGS.</th>
            <th class="text-center text-white">BATH / KGS.</th>
            <th class="text-center text-white">PRICE</th>
            <th class="text-center text-white">VAT</th>
            <th class="text-center text-white">TOTAL</th>
            <th class="text-center text-white">SALES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0);
        $sql = " SELECT bs_orders.id, bs_orders.code,bs_orders.customer_id,
		bs_orders.customer_name,bs_orders.date,bs_orders.sales ,bs_orders.user,bs_orders.parent,
		bs_orders.amount,bs_orders.price,bs_orders.vat_type,bs_orders.vat,bs_orders.total,bs_orders.net,bs_orders.delivery_date,
		bs_orders.delivery_time,bs_orders.status,bs_employees.fullname,bs_orders.product_id
		FROM bs_orders
        LEFT OUTER JOIN bs_employees ON bs_orders.sales = bs_employees.id
		where   delivery_date IS NULL AND DATE(bs_orders.created) = '" . $_POST['date'] . "' AND bs_orders.status > 0 ";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            echo '<tr>';
            echo '<td class="text-center">' . $order['date'] . '</td>';
            echo '<td class="text-center">' . $order['code'] . '</td>';
            echo '<td class="text-center">' . $order['customer_name'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $order['fullname'] . '</td>';
            echo '</tr>';
            $aSum[0] += 1;
            $aSum[1] += $order['amount'];
            $aSum[2] += $order['total'];
            $aSum[3] += $order['vat'];
            $aSum[4] += $order['net'];
        }

        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="3">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
            <th class="text-right"><?php echo number_format($aSum[1], 4) ?></th>
            <th></th>
            <th class="text-right"><?php echo number_format($aSum[2], 2) ?></th>
            <th class="text-right"><?php echo number_format($aSum[3], 2) ?></th>
            <th class="text-right"><?php echo number_format($aSum[4], 2) ?></th>
            <th class="text-center" colspan="2"></th>
        </tr>
    </tfoot>
</table>