<?php
$start_date = "2022-05-05";
$start_balance = 10482;
$start_net = 275357246.03;


$sql = "SELECT
		SUM(bs_orders.amount) AS amount,
		SUM(bs_orders.total) AS total,
		SUM(bs_orders.net) AS net,
		COUNT(bs_orders.id) AS total_order
	FROM bs_orders
	WHERE DATE(date) LIKE '" . $date . "' AND bs_orders.status > 0;
	";
$rst = $dbc->Query($sql);
$total = $dbc->Fetch($rst);

$sql = "SELECT
		SUM(bs_orders.amount) AS amount,
		SUM(bs_orders.total) AS total,
		SUM(bs_orders.net) AS net,
		COUNT(bs_orders.id) AS total_order
	FROM bs_deliveries
	LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
	WHERE DATE(bs_deliveries.delivery_date) LIKE '" . $date . "' AND bs_orders.status > 0;
	";
$rst = $dbc->Query($sql);
$delivery = $dbc->Fetch($rst);

$sql = "SELECT
		SUM(bs_orders.amount) AS amount,
		SUM(bs_orders.total) AS total,
		SUM(bs_orders.net) AS net,
		COUNT(bs_orders.id) AS total_order
	FROM bs_orders
	WHERE DATE(date) < '" . $date . "' AND DATE(date) >= '" . $start_date . "' AND bs_orders.status > 0;
	
	";
$rst = $dbc->Query($sql);
$balance = $dbc->Fetch($rst);
$balance['amount'] += $start_balance;
$balance['net'] += $start_net;



$sql = "SELECT
		SUM(bs_orders.amount) AS amount,
		SUM(bs_orders.total) AS total,
		SUM(bs_orders.net) AS net,
		COUNT(bs_orders.id) AS total_order
	FROM bs_deliveries
	LEFT JOIN bs_orders ON bs_deliveries.id = bs_orders.delivery_id
	WHERE DATE(bs_deliveries.delivery_date) < '" . $date . "' 
		AND DATE(bs_deliveries.delivery_date) >= '" . $start_date . "' 
		AND bs_orders.status > 0;
		
	";

$rst = $dbc->Query($sql);
$balance_delivery = $dbc->Fetch($rst);
$balance['amount'] -= $balance_delivery['amount'];
$balance['net'] -= $balance_delivery['net'];

?>

<div class="row gutters-sm">
	<div class="col-xl-4">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<tbody>
				<tr>
					<th class="text-center table-dark font-weight-bold">TODAY KG.</th>
				</tr>
				<tr>
					<td class="text-center"><?php echo $total['amount']; ?></td>
				</tr>
				<tr>
					<th class="text-center table-dark font-weight-bold">ORDER NO.</th>
				</tr>
				<tr>
					<td class="text-center"><?php echo $total['total_order']; ?></td>
				</tr>
			</tbody>
		</table>
		<button onclick="fn.dialog.open('apps/sales_overview/view/dialog.lock.lookup.php','#dialog_lock_lookup')" type="button" class="btn btn-warning">Lock Report</button>
	</div>
	<div class="col-xl-8">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<tbody>
				<tr>
					<th class="text-left table-dark font-weight-bold">B/F</th>
					<td class="text-center"><?php echo number_format($balance['amount'], 4); ?></td>
					<td class="text-center"><?php echo number_format($balance['net'], 4); ?></td>
				</tr>
				<tr>
					<th class="text-left table-dark font-weight-bold">TODAY SALE</th>
					<td class="text-center"><?php echo number_format($total['amount'], 4); ?></td>
					<td class="text-center"><?php echo number_format($total['net'], 4); ?></td>
				</tr>
				<tr>
					<th class="text-left table-dark font-weight-bold">TODAY DELIVERY</th>
					<td class="text-center"><?php echo number_format(-$delivery['amount'], 4); ?></td>
					<td class="text-center"><?php echo number_format(-$delivery['net'], 4); ?></td>
				</tr>
				<tr>
					<th class="text-left table-dark font-weight-bold">ADJUST</th>
					<td class="text-center">0.000</td>
					<td class="text-center">0.00</td>
				</tr>
				<?php
				$balance['amount'] += $total['amount'] - $delivery['amount'];
				$balance['net'] += $total['net'] - $delivery['net'];
				?>
				<tr>
					<th class="text-left table-dark font-weight-bold">C/F</th>
					<td class="text-center"><?php echo number_format($balance['amount'], 4); ?></td>
					<td class="text-center"><?php echo number_format($balance['net'], 4); ?></td>
				</tr>

			</tbody>
		</table>
	</div>
</div>