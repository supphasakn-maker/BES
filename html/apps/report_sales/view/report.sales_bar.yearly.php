<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <td class="text-center text-white"></td>
            <td class="text-center text-white">MONTHLY</td>
            <th class="text-center text-white">ORDER</th>
            <th class="text-center text-white">KGS.</th>
            <th class="text-center text-white">PRICE</th>
            <th class="text-center text-white">VAT</th>
            <th class="text-center text-white">TOTAL</th>

        </tr>
    </thead>
    <tbody>



        <?php
        $aSum = array(0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(id) AS total_order,
			MONTH(date) AS month,
			SUM(amount) AS amount,
			SUM(price) AS price,
			SUM(total) AS total,
			SUM(vat) AS vat,
			SUM(net) AS net,
			DATE_FORMAT(date,'%Y-%m') AS sql_month
		FROM bs_orders WHERE YEAR(date) = '" . $_POST['year'] . "' AND bs_orders.parent IS NULL  AND bs_orders.status > -1 AND bs_orders.product_id = '2' 
		GROUP BY MONTH(date)
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            echo '<tr class=" text-dark">';
            echo '<td>';
            echo '<button class="btn btn-sm btn-primary" onclick="$(this).parent().parent().next().toggleClass(\'d-none\')">Toggle</button>';
            echo '</td>';
            echo '<td class="text-center">' . $order['sql_month'] . '</td>';
            echo '<td class="text-center">' . $order['total_order'] . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';
            $aSum[0] += $order['total_order'];
            $aSum[1] += $order['amount'];
            $aSum[2] += $order['total'];
            $aSum[3] += $order['vat'];
            $aSum[4] += $order['net'];

            echo '<tr class=" d-none">';
            echo '<td colspan="6">';

        ?>
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

                    // $sql = "SELECT * FROM bs_orders WHERE DATE_FORMAT(date,'%Y-%m')='".$order['sql_month']."' AND bs_orders.status > -1";
                    $sql = " SELECT bs_orders.id, bs_orders.code,bs_orders.customer_id,
		bs_orders.customer_name,bs_orders.date,bs_orders.sales ,bs_orders.user,bs_orders.parent,
		bs_orders.amount,bs_orders.price,bs_orders.vat_type,bs_orders.vat,bs_orders.total,bs_orders.net,bs_orders.delivery_date,
		bs_orders.delivery_time,bs_orders.status,bs_deliveries.billing_id,bs_orders.product_id
		FROM bs_orders
		LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id 
		 WHERE DATE_FORMAT(bs_orders.date,'%Y-%m')='" . $order['sql_month'] . "' AND bs_orders.status > -1 AND bs_orders.product_id = '2' ";
                    $rst_daily = $dbc->Query($sql);
                    while ($item = $dbc->Fetch($rst_daily)) {
                        $employee_name = "-";
                        if ($item['sales'] != null) {
                            $employee = $dbc->GetRecord("bs_employees", "*", "id=" . $item['sales']);
                            if (is_array($employee)) $employee_name = $employee['fullname'];
                        }
                        echo '<tr>';
                        // echo '<td class="text-center">'.$item['date'].'</td>';
                        // echo '<td class="text-center">'.$item['delivery_id'].'</td>';
                        // echo '<td class="text-center">'.$item['code'].'</td>';
                        // echo '<td class="text-center">'.$item['customer_name'].'</td>';
                        // echo '<td class="text-right">'.number_format($item['amount'],4).'</td>';
                        // echo '<td class="text-right">'.number_format($item['price'],2).'</td>';
                        // echo '<td class="text-right">'.number_format($item['total'],2).'</td>';
                        // echo '<td class="text-right">'.number_format($item['vat'],2).'</td>';
                        // echo '<td class="text-right">'.number_format($item['net'],2).'</td>';
                        // echo '<td class="text-center">'.$item['delivery_date'].'</td>';
                        // echo '<td class="text-center">'.$employee_name.'</td>';
                        // echo '<td class="text-center">'.$item['billing_id'].'</td>';

                        echo '<td class="text-center">' . $item['date'] . '</td>';
                        echo '<td class="text-center">' . $item['code'] . '</td>';
                        echo '<td class="text-center">' . $item['billing_id'] . '</td>';
                        echo '<td class="text-center">' . $item['customer_name'] . '</td>';
                        echo '<td class="text-right">' . number_format($item['amount'], 4) . '</td>';
                        echo '<td class="text-right">' . number_format($item['price'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['total'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['vat'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['net'], 2) . '</td>';
                        echo '<td class="text-center">' . $item['delivery_date'] . '</td>';
                        echo '<td class="text-center">' . $employee_name . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php



            echo '</td>';
            echo '</tr>';
        }

        ?>
    </tbody>
    <tfoot class="bg-dark">
        <tr>
            <th class="text-center  text-white" colspan="3">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSum[1], 4) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSum[2], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSum[3], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSum[4], 2) ?></th>
        </tr>
    </tfoot>
</table>