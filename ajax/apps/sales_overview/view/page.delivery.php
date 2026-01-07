<div class="mb-2">
	<a href="#apps/sales_overview/index.php" class="btn btn-outline-dark">Back</a>
</div>

	<div class="card mb-3">
		<div class="card-body">
<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center table-dark" colspan="10">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y",strtotime($date));?></tjh>
		</tr>
		<tr>
			<th class="text-center">วันสั่ง</th>
			<th class="text-center">Delivery No.</th>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">kio</th>
			<th class="text-center">บาท/กิโล</th>
			<th class="text-center">ยอดรวม</th>
			<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
			<th class="text-center">ยอดรวมทั้งหมด</th>
			<th class="text-center">ช่วงเวลา</th>
			<th class="text-center">คนส่ง</th>
		</tr>
	</thead>
	<!-- /Filter columns -->
	<tbody>
	<?php
	if(isset($_GET['date'])){
		$date = $_GET['date'];
	}
		
		$total = array(0,0,0,0);
		$sql = "SELECT 
				bs_orders.date AS date,
				bs_deliveries.delivery_date AS delivery_date,
				bs_deliveries.code AS code,
				bs_orders.customer_name AS customer_name,
				bs_orders.amount AS amount,
				bs_orders.price AS price,
				bs_orders.total AS total,
				bs_orders.vat AS vat,
				bs_orders.net AS net,
				bs_orders.delivery_time AS delivery_time
			FROM bs_deliveries
			LEFT JOIN bs_orders ON bs_orders.delivery_id =  bs_deliveries.id
			WHERE
				DATE(bs_deliveries.delivery_date) = '".$date."'
				AND bs_orders.status > 0
			ORDER BY bs_orders.delivery_date ASC
				";
		$rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){
			echo '<tr>';
			/*
				echo '<td class="text-Center">'.date("d/m/Y",strtotime($date)).'</td>';
				echo '<td class="text-Center">'.$order['code'].'</td>';
				echo '<td class="text-Center">'.$order['customer_name'].'</td>';
				
				*/
				echo '<td>'.date("d/m/Y",strtotime($order['date'])).'</td>';
				echo '<td>'.$order['code'].'</td>';
				echo '<td>'.$order['customer_name'].'</td>';
				echo '<td class="text-right">'.number_format($order['amount'],4).'</td>';
				echo '<td class="text-right">'.number_format($order['price'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['total'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['vat'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['net'],2).'</td>';
				echo '<td>'.$order['delivery_time'].'</td>';
				echo '<td>-</td>';
			echo '</tr>';
			$total[0]+=$order['amount'];
			$total[1]+=$order['total'];
			$total[2]+=$order['vat'];
			$total[3]+=$order['net'];
		}
	?>
	
	</tbody>
	<thead>
		<tr>
			<th colspan="3" class="text-right">รวม</th>
			<th class="text-right"><?php echo number_format($total[0],4);?></th>
			<th></th>
			<th class="text-right"><?php echo number_format($total[1],2);?></th>
			<th class="text-right"><?php echo number_format($total[2],2);?></th>
			<th class="text-right"><?php echo number_format($total[3],2);?></th>
			<th colspan="2"></th>
		</tr>
	</thead>
</table>
		</div>
	</div>
