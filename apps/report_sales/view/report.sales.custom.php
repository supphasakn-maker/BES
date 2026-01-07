<table class="table table-sm table-bordered table-striped">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white">DATE ADD</th>
			<th class="text-center text-white">ORDER NO.</th>
			<th class="text-center text-white">INVOICE NO.</th>
			<th class="text-center text-white">CUSTOMER</th>
			<th class="text-center text-white">KGS.</th>
			<th class="text-center text-white">BATH / KGS.</th>
			<th class="text-center text-white">VAT</th>
			<th class="text-center text-white">TOTAL</th>
			<th class="text-center text-white">DELIVERY DATE</th>
			<th class="text-center text-white">SALES</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$aSum = array(0, 0, 0, 0, 0);

		// $sql = "SELECT * FROM bs_orders WHERE date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_orders.status > -1";
		$sql = " SELECT bs_orders.id, bs_orders.code,bs_orders.customer_id,
		bs_orders.customer_name,bs_orders.date,bs_orders.sales ,bs_orders.user,bs_orders.parent,
		bs_orders.amount,bs_orders.price,bs_orders.vat_type,bs_orders.vat,bs_orders.total,bs_orders.net,bs_orders.delivery_date,
		bs_orders.delivery_time,bs_orders.status,bs_deliveries.billing_id,bs_employees.fullname,bs_orders.product_id
		FROM bs_orders
		LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id  
		LEFT OUTER JOIN bs_employees ON bs_orders.sales = bs_employees.id
		where  DATE(bs_orders.date) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "' AND bs_orders.parent IS NULL  AND bs_orders.status > -1 ";
		$rst = $dbc->Query($sql);
		while ($order = $dbc->Fetch($rst)) {
			if ($order['product_id'] == '4') {
				$bgsuccess = 'class="bg-success text-white font-weight-bold"';
			} else {
				$bgsuccess = '';
			}
			echo '<tr ' . $bgsuccess . '>';
			echo '<td class="text-center">' . $order['date'] . '</td>';
			echo '<td class="text-center">' . $order['code'] . '</td>';
			echo '<td class="text-center">' . $order['billing_id'] . '</td>';
			echo '<td class="text-center">' . $order['customer_name'] . '</td>';
			echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
			echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
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

<!-- ตารางเฉพาะ USD -->
<div class="mt-4">
	<h4 class="text-center">รายการขาย USD </h4>
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
				<th class="text-center text-white">USD</th>
				<th class="text-center text-white">DELIVERY DATE</th>
				<th class="text-center text-white">SALES</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$aSumUSD = array(0, 0, 0, 0, 0, 0); // เพิ่ม index สำหรับ USD
			$sql_usd = " SELECT bs_orders.id, bs_orders.code,bs_orders.customer_id,
			bs_orders.customer_name,bs_orders.date,bs_orders.sales ,bs_orders.user,bs_orders.parent,
			bs_orders.amount,bs_orders.price,bs_orders.vat_type,bs_orders.vat,bs_orders.total,bs_orders.net,bs_orders.usd,bs_orders.delivery_date,
			bs_orders.delivery_time,bs_orders.status,bs_deliveries.billing_id,bs_employees.fullname,bs_orders.product_id
			FROM bs_orders
			LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id  
			LEFT OUTER JOIN bs_employees ON bs_orders.sales = bs_employees.id
			where  DATE(bs_orders.date) BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "'  AND bs_orders.parent IS NULL  AND bs_orders.status > -1 AND bs_orders.usd > 0 ";
			$rst_usd = $dbc->Query($sql_usd);
			while ($order = $dbc->Fetch($rst_usd)) {
				if ($order['product_id'] == '4') {
					$bgsuccess = 'class="bg-success text-white font-weight-bold"';
				} else {
					$bgsuccess = '';
				}
				echo '<tr ' . $bgsuccess . '>';
				echo '<td class="text-center">' . $order['date'] . '</td>';
				echo '<td class="text-center">' . $order['code'] . '</td>';
				echo '<td class="text-center">' . $order['billing_id'] . '</td>';
				echo '<td class="text-center">' . $order['customer_name'] . '</td>';
				echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
				echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
				echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
				echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
				echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
				echo '<td class="text-right text-success font-weight-bold">' . number_format($order['usd'], 2) . '</td>';
				echo '<td class="text-center">' . $order['delivery_date'] . '</td>';
				echo '<td class="text-center">' . $order['fullname'] . '</td>';
				echo '</tr>';
				$aSumUSD[0] += 1;
				$aSumUSD[1] += $order['amount'];
				$aSumUSD[2] += $order['total'];
				$aSumUSD[3] += $order['vat'];
				$aSumUSD[4] += $order['net'];
				$aSumUSD[5] += $order['usd'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th class="text-center" colspan="4">รวม USD <?php echo $aSumUSD[0]; ?> รายการ</th>
				<th class="text-right"><?php echo number_format($aSumUSD[1], 4) ?></th>
				<th></th>
				<th class="text-right"><?php echo number_format($aSumUSD[2], 2) ?></th>
				<th class="text-right"><?php echo number_format($aSumUSD[3], 2) ?></th>
				<th class="text-right"><?php echo number_format($aSumUSD[4], 2) ?></th>
				<th class="text-right text-success font-weight-bold">$<?php echo number_format($aSumUSD[5], 2) ?></th>
				<th class="text-center" colspan="2"></th>
			</tr>
		</tfoot>
	</table>
</div>

<!-- สรุปเปรียบเทียบ -->
<div class="mt-4">
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h5 class="mb-0">สรุปยอดขายรวม</h5>
				</div>
				<div class="card-body">
					<table class="table table-sm">
						<tr>
							<td><strong>จำนวนรายการทั้งหมด:</strong></td>
							<td class="text-right"><?php echo number_format($aSum[0]); ?> รายการ</td>
						</tr>
						<tr>
							<td><strong>ยอดรวม (TOTAL + VAT) ทั้งหมด:</strong></td>
							<td class="text-right"><?php echo number_format($aSum[4], 2); ?> บาท</td>
						</tr>
						<tr class="table-info">
							<td><strong>ยอดรวม THB :</strong></td>
							<td class="text-right font-weight-bold"><?php echo number_format(($aSum[4] - $aSumUSD[4]), 2); ?> บาท</td>
						</tr>

						<tr>
							<td><strong>น้ำหนักรวม:</strong></td>
							<td class="text-right"><?php echo number_format($aSum[1], 4); ?> กก.</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h5 class="mb-0">สรุปยอดขาย USD</h5>
				</div>
				<div class="card-body">
					<table class="table table-sm">
						<tr>
							<td><strong>จำนวนรายการ USD:</strong></td>
							<td class="text-right"><?php echo number_format($aSumUSD[0]); ?> รายการ</td>
						</tr>
						<tr>
							<td><strong>ยอดรวม (TOTAL + VAT):</strong></td>
							<td class="text-right"><?php echo number_format($aSumUSD[4], 2); ?> บาท</td>
						</tr>
						<tr class="table-info">
							<td><strong>ยอดรวม USD:</strong></td>
							<td class="text-right text-success font-weight-bold">$<?php echo number_format($aSumUSD[5], 2); ?></td>
						</tr>
						<tr>
							<td><strong>น้ำหนักรวม:</strong></td>
							<td class="text-right"><?php echo number_format($aSumUSD[1], 4); ?> กก.</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>