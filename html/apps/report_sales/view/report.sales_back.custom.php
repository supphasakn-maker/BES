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
            <th class="text-center text-white">DATE RECEIVED</th>
            <th class="text-center text-white">SALES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0);
        $sql = " SELECT DISTINCT(bs_stock_silver.submited),bs_orders_buy.id, bs_orders_buy.code,bs_orders_buy.customer_id,bs_orders_buy.created,
        bs_orders_buy.sales ,bs_orders_buy.amount,bs_orders_buy.price,bs_orders_buy.vat_type,
        bs_orders_buy.vat,bs_orders_buy.total,bs_orders_buy.net,bs_orders_buy.status,bs_employees.fullname,
        bs_customers.name
		FROM bs_orders_buy
		LEFT OUTER JOIN bs_stock_silver ON bs_orders_buy.id = bs_stock_silver.customer_po  
		LEFT OUTER JOIN bs_employees ON bs_orders_buy.sales = bs_employees.id
        LEFT OUTER JOIN bs_customers ON bs_orders_buy.customer_id = bs_customers.id
		where  bs_orders_buy.created BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_orders_buy.status > -1 ";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            echo '<tr>';
            echo '<td class="text-center">' . $order['created'] . '</td>';
            echo '<td class="text-center">' . $order['code'] . '</td>';
            echo '<td class="text-center">' . $order['name'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $order['submited'] . '</td>';
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
            <th class="text-center" colspan="3"></th>
        </tr>
    </tfoot>
</table>