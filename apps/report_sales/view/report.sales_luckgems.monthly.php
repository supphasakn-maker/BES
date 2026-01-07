<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <th class="text-center text-white">DATE ADD</th>
            <th class="text-center text-white">ORDER NO.</th>
            <th class="text-center text-white">INVOICE NO.</th>
            <th class="text-center text-white">CUSTOMER</th>
            <th class="text-center text-white">KGS.</th>
            <th class="text-center text-white">BATH / KGS.</th>
            <th class="text-center text-white">PRICE</th>
            <th class="text-center text-white">VAT</th>
            <th class="text-center text-white">TOTAL</th>
            <th class="text-center text-white">DELIVERY DATE</th>
            <th class="text-center text-white">SALES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0);

        // $sql = "SELECT * FROM bs_orders WHERE DATE(date) = '".$_POST['date']."' AND bs_orders.parent IS NULL  AND bs_orders.status > -1";
        $sql = " SELECT bs_orders.id, bs_orders.code,bs_orders.customer_id,
		bs_orders.customer_name,bs_orders.date,bs_orders.sales ,bs_orders.user,bs_orders.parent,
		bs_orders.amount,bs_orders.price,bs_orders.vat_type,bs_orders.vat,bs_orders.total,bs_orders.net,bs_orders.delivery_date,
		bs_orders.delivery_time,bs_orders.status,bs_deliveries.billing_id,bs_employees.fullname,bs_orders.product_id
		FROM bs_orders
		LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id  
		LEFT OUTER JOIN bs_employees ON bs_orders.sales = bs_employees.id
        LEFT OUTER JOIN bs_stock_adjusted ON bs_orders.code = bs_stock_adjusted.remark
		where  DATE_FORMAT(bs_orders.date,'%Y-%m') = '" . $_POST['month'] . "' AND bs_orders.parent IS NULL  AND bs_orders.status > -1 AND bs_stock_adjusted.type_id = 12 ";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . $order['date'] . '</td>';
            echo '<td class="text-center">' . $order['code'] . '</td>';
            echo '<td class="text-center">' . $order['billing_id'] . '</td>';
            echo '<td class="text-center">' . $order['customer_name'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $order['delivery_date'] . '</td>';
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
            <th class="text-center" colspan="4">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
            <th class="text-right"><?php echo number_format($aSum[1], 4) ?></th>
            <th></th>
            <th class="text-right"><?php echo number_format($aSum[2], 2) ?></th>
            <th class="text-right"><?php echo number_format($aSum[3], 2) ?></th>
            <th class="text-right"><?php echo number_format($aSum[4], 2) ?></th>
            <th class="text-center" colspan="3"></th>
        </tr>
    </tfoot>
</table>