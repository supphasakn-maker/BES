<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr>
            <td class="text-center">วันที่ส่ง</td>
            <th class="text-center">จำนวนการสั่งซื้อ</th>
            <th class="text-center">จำนวนกิโลกรัม</th>
            <th class="text-center">ยอดรวม</th>
            <th class="text-center">ภาษีมูลค่าเพิ่ม</th>
            <th class="text-center">ยอดรวมสุทธิ</th>

        </tr>
    </thead>
    <tbody>



        <?php
        $aSum = array(0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(id) AS total_order,
			delivery_date AS date,
			SUM(amount) AS amount,
			SUM(price) AS price,
			SUM(total) AS total,
			SUM(vat) AS vat,
			SUM(net) AS net
		FROM bs_orders WHERE DATE_FORMAT(delivery_date,'%Y-%m') = '" . $_POST['month'] . "'
		AND bs_orders.status > 0 AND bs_orders.product_id = '2' 
		GROUP BY delivery_date 
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            echo '<tr>';
            echo '<td class="text-center">' . $order['date'] . '</td>';
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
        }

        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="2">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
            <th class="text-right pr-2"><?php echo number_format($aSum[1], 4) ?></th>
            <th class="text-right pr-2"><?php echo number_format($aSum[2], 2) ?></th>
            <th class="text-right pr-2"><?php echo number_format($aSum[3], 2) ?></th>
            <th class="text-right pr-2"><?php echo number_format($aSum[4], 2) ?></th>
        </tr>
    </tfoot>
</table>