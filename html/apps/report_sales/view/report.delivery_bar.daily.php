<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr>
            <td class="text-center">วันที่จัดส่ง</td>
            <td class="text-center">เวลาจัดส่ง</td>
            <th class="text-center">หมายเลขจัดส่ง</th>
            <th class="text-center">หมายเลขสั่งซื้อ</th>
            <th class="text-center">ลูกค้า</th>
            <th class="text-center">จำนวน</th>
            <th class="text-center">บาท/กิโล</th>
            <th class="text-center">ยอดรวม</th>
            <th class="text-center">ภาษีมูลค่าเพิ่ม</th>
            <th class="text-center">ยอดรวมสุทธิ</th>
            <th class="text-center">ผู้ขาย</th>
            <th class="text-center">หมายเลขบิล</th>
            <th class="text-center">ผู้ส่ง</th>
            <th class="text-center">วิธีชำระเงิน</th>
            <th class="text-center">Clearing</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0);
        $sql = "SELECT
				bs_orders.delivery_date AS date,
				bs_orders.delivery_time AS time,
				bs_orders.code AS order_code,
				bs_deliveries.code AS delivery_code,
				bs_orders.customer_name AS customer_name,
				bs_orders.amount AS amount,
				bs_orders.price AS price,
				bs_orders.total AS total,
				bs_orders.vat AS vat,
				bs_orders.net AS net,
				bs_orders.sales AS sales,
				bs_orders.info_payment AS info_payment,
				bs_deliveries.billing_id AS billing_id,
				bs_deliveries.id AS delivery_id
				
			FROM bs_deliveries 
			LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
			WHERE bs_orders.delivery_date = '" . $_POST['date'] . "'
			AND bs_orders.status > 0 AND bs_orders.product_id = '2' 
			
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            $employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
            echo '<tr>';
            echo '<td class="text-center">' . $order['date'] . '</td>';
            echo '<td class="text-center">' . $order['time'] . '</td>';
            echo '<td class="text-center">' . $order['order_code'] . '</td>';
            echo '<td class="text-center">' . $order['delivery_code'] . '</td>';
            echo '<td class="text-center">' . $order['customer_name'] . '</td>';
            echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
            echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $employee['fullname'] . '</td>';
            echo '<td class="text-center">' . $order['billing_id'] . '</td>';
            echo '<td>';
            $sql = "SELECT 
						bs_deliveries_drivers.id AS mapping_id,
						bs_deliveries_drivers.truck_type AS truck_type,
						bs_deliveries_drivers.truck_license AS truck_license,
						bs_deliveries_drivers.time_departure AS time_departure,
						bs_deliveries_drivers.time_departure AS time_departure,
						bs_deliveries_drivers.time_arrive AS time_arrive,
						bs_employees.fullname AS fullname
						
					FROM bs_deliveries_drivers 
					LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
					WHERE 
					delivery_id =" . $order['delivery_id'];
            $rst_driver = $dbc->Query($sql);
            while ($driver = $dbc->Fetch($rst_driver)) {
                echo '<span class="badge badge-dark">' . $driver['fullname'] . '</span>';
            }
            echo '</td>';
            echo '<td class="text-center">' . $order['info_payment'] . '</td>';
            echo '<td class="text-center">-</td>';
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