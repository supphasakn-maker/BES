<div class="mb-2">
	<a href="#apps/sales_overview_bwd/index.php" class="btn btn-outline-dark">Back</a>
</div>

	<div class="card mb-3">
		<div class="card-body">
<table class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center table-dark font-weight-bold" colspan="11">ภาพรวมจัดส่งประจำวันที่ <?php echo date("d/m/Y",strtotime($date));?></tjh>
		</tr>
		<tr>
			<th class="text-center table-dark font-weight-bold">DATE</th>
			<th class="text-center table-dark font-weight-bold">BILL NO.</th>
			<th class="text-center table-dark font-weight-bold">ORDER NO.</th>
			<th class="text-center table-dark font-weight-bold">CUSTOMER</th>
			<th class="text-center table-dark font-weight-bold">BARS</th>
			<th class="text-center table-dark font-weight-bold">BATH / KGS.</th>
			<th class="text-center table-dark font-weight-bold">TOTAL</th>
			<th class="text-center table-dark font-weight-bold">TOTAL - DISCOUNT</th>
			<th class="text-center table-dark font-weight-bold">PERIOD</th>
			<th class="text-center table-dark font-weight-bold">MSG.</th>
			<th class="text-center table-dark font-weight-bold"></th>
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
				bs_orders_bwd.id AS id,
				bs_orders_bwd.date AS date,
				bs_deliveries_bwd.delivery_date AS delivery_date,
				bs_deliveries_bwd.billing_id AS code,
				bs_orders_bwd.customer_name AS customer_name,
				bs_orders_bwd.amount AS amount,
				bs_orders_bwd.price AS price,
				bs_orders_bwd.total AS total,
				bs_orders_bwd.discount AS discount,
				bs_orders_bwd.net AS net,
				bs_orders_bwd.code AS order_number,
				bs_orders_bwd.delivery_time AS delivery_time
			FROM bs_deliveries_bwd
			LEFT JOIN bs_orders_bwd ON bs_orders_bwd.delivery_id =  bs_deliveries_bwd.id
			WHERE
				DATE(bs_deliveries_bwd.delivery_date) = '".$date."'
				AND bs_orders_bwd.status > 0
			ORDER BY bs_orders_bwd.delivery_date ASC
				";
		$rst = $dbc->Query($sql);
		while($order = $dbc->Fetch($rst)){
			echo '<tr>';
				echo '<td>'.date("d/m/Y",strtotime($order['date'])).'</td>';
				echo '<td>'.$order['code'].'</td>';
				echo '<td>'.$order['order_number'].'</td>';
				echo '<td>'.$order['customer_name'].'</td>';
				echo '<td class="text-right">'.number_format($order['amount'],4).'</td>';
				echo '<td class="text-right">'.number_format($order['price'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['total'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['discount'],2).'</td>';
				echo '<td class="text-right">'.number_format($order['net'],2).'</td>';
				echo '<td>'.$order['delivery_time'].'</td>';
				echo '<td>-</td>';
			echo '</tr>';
			$total[0]+=$order['amount'];
			$total[1]+=$order['total'];
			$total[2]+=$order['discount'];
			$total[3]+=$order['net'];
		}
	?>
	
	</tbody>
	<thead>
		<tr>
			<th colspan="4" class="text-right">รวม</th>
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
