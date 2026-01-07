<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center table-dark" colspan="10">รายงานแยกตามวันส่ง</th>
		</tr>
		<tr>
			<th class="text-center">วันที่ส่ง</th>
			<th class="text-center">kio</th>
			<th class="text-center">ยอดรวม</th>
			<th class="text-center">ยอดรวม (vat)</th>
		</tr>
	</thead>
	<!-- /Filter columns -->
	<tbody>
	<?php
		$total_amount =0;
		$total_total = 0;
		$total_net = 0;
		$sql = "
			SELECT 
				bs_deliveries.delivery_date AS delivery_date,
				COUNT(bs_deliveries.id) AS id,
				SUM(bs_orders.amount) AS amount,
				SUM(bs_orders.total) AS total,
				SUM(bs_orders.net) AS net
			FROM bs_deliveries
			LEFT JOIN bs_orders ON bs_orders.delivery_id =  bs_deliveries.id
			WHERE bs_orders.status > 0 AND bs_deliveries.delivery_date >= '".$date."' 
			GROUP BY bs_deliveries.delivery_date
			ORDER BY bs_deliveries.delivery_date ASC
				
				";
		$rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td class="text-center">'.date("d/m/Y",strtotime($order['delivery_date'])).'</td>';
				echo '<td class="text-right">'.number_format($order['amount'],4).'</td>';
				echo '<td class="text-right">'.number_format($order['total'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['net'],2).'</td>';
			echo '</tr>';
			$total_amount += $order['amount'];
			$total_total += $order['total'];
			$total_net += $order['net'];
		}
	?>
	
	</tbody>
	<thead>
		<tr>
			<th class="text-right">รวม</th>
			<th class="text-right"><?php echo number_format($total_amount,4);?></th>
			<th class="text-right"><?php echo number_format($total_total,2);?></th>

			<th class="text-right"><?php echo number_format($total_net,2);?></th>
		
		</tr>
	</thead>
</table>