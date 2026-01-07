<table class="table table-sm table-bordered table-striped">
	<thead>
		<tr>
			<td class="text-center">วันสั่ง</td>
			<th class="text-center">Delivery No.</th>
			<th class="text-center">Order No.</th>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">kio</th>
			<th class="text-center">บาท/กิโล</th>
			<th class="text-center">ยอดรวม</th>
			<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
			<th class="text-center">ยอดรวมสุทธิ</th>
			<th class="text-center">วันส่ง</th>
			<th class="text-center">ผู้ขาย</th>
			<th class="text-center">หมายเลขบิล</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$aSum = array(0,0,0,0,0);
		$sql = "SELECT * FROM bs_orders WHERE date BETWEEN '".$_POST['date_from']."' AND '".$_POST['date_to']."' AND bs_orders.status > -1";
		$rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){
			
			$employee = $dbc->GetRecord("bs_employees","*","id=".$order['sales']);
			echo '<tr>';
				echo '<td class="text-center">'.$order['date'].'</td>';
				echo '<td class="text-center">'.$order['delivery_id'].'</td>';
				echo '<td class="text-center">'.$order['code'].'</td>';
				echo '<td class="text-center">'.$order['customer_name'].'</td>';
				echo '<td class="text-right">'.number_format($order['amount'],4).'</td>';
				echo '<td class="text-right">'.number_format($order['price'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['total'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['vat'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['net'],2).'</td>';
				echo '<td class="text-center">'.$order['delivery_date'].'</td>';
				echo '<td class="text-center">'.$employee['fullname'].'</td>';
				echo '<td class="text-center">'.$order['billing_id'].'</td>';
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
			<th class="text-right"><?php echo number_format($aSum[1],4)?></th>
			<th></th>
			<th class="text-right"><?php echo number_format($aSum[2],2)?></th>
			<th class="text-right"><?php echo number_format($aSum[3],2)?></th>
			<th class="text-right"><?php echo number_format($aSum[4],2)?></th>
			<th class="text-center" colspan="3"></th>
		</tr>
	</tfoot>
</table>
