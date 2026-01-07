<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap text-danger">
	<thead>
		<tr>
			<th class="text-center table-danger font-weight-bold" colspan="13">รายการที่ถูกลบ <?php echo date("d/m/Y", strtotime($date)); ?></th>
		</tr>
		<tr>
			<th class="text-center table-danger font-weight-bold">PO</th>
			<th class="text-center table-danger font-weight-bold">CUSTOMER</th>
			<th class="text-center table-danger font-weight-bold">KIO</th>
			<th class="text-center table-danger font-weight-bold">BATH / KGS.</th>
			<th class="text-center table-danger font-weight-bold">VAT</th>
			<th class="text-center table-danger font-weight-bold">TOTAL</th>
			<th class="text-center table-danger font-weight-bold">TOTAL + VAT</th>
			<th class="text-center table-danger font-weight-bold">DELIVERY DATE</th>
			<th class="text-center table-danger font-weight-bold">SALE</th>
			<th class="text-center table-danger font-weight-bold">SP</th>
			<th class="text-center table-danger font-weight-bold">EX</th>
			<th class="text-center table-danger font-weight-bold">REMARK</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total_amount = 0;
		$total_vat = 0;
		$total_total = 0;
		$total_net = 0;
		$counter = 0;

		$sql = "SELECT * FROM bs_orders WHERE DATE(date) LIKE '" . $date . "' AND bs_orders.status = -1";
		$rst = $dbc->Query($sql);
		while ($order = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-Center">' . $order['code'] . '</td>';
			echo '<td class="text-Center">' . $order['customer_name'] . '</td>';
			echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
			echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['vat'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['total'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
			echo '<td class="text-Center">' . $order['delivery_date'] . '</td>';
			echo '<td class="text-Center">';
			if ($order['sales'] != "") {
				$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $order['sales']);
				echo $employee['fullname'];
			} else {
				echo "-";
			}
			echo '</td>';
			echo '<td class="text-right">' . number_format($order['rate_spot'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['rate_exchange'], 2) . '</td>';
			echo '<td class="text-Center">' . $order['remove_reason'] . '</td>';
			echo '</tr>';

			$total_amount += $order['amount'];
			$total_vat += $order['vat'];
			$total_total += $order['total'];
			$total_net += $order['net'];
			$counter++;
		}




		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center" colspan="2">
				จากเอกสารทั้งหมด <?php echo $counter; ?> รายการ
			</th>
			<th class="text-center"><?php echo number_format($total_amount, 4); ?></th>
			<th class="text-center"></th>
			<th class="text-center"><?php echo number_format($total_vat, 2); ?></th>
			<th class="text-center"><?php echo number_format($total_total, 2); ?></th>
			<th class="text-center"><?php echo number_format($total_net, 2); ?></th>
			<th class="text-center" colspan="2"></th>
		</tr>
	</tfoot>
</table>