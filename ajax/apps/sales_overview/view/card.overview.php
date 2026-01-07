<?php
	$sql = "SELECT
		SUM(bs_orders.amount) AS amount,
		COUNT(bs_orders.id) AS total
	FROM bs_orders
	WHERE DATE(date) LIKE '".$date."' AND bs_orders.status > 0;
	";
	$rst = $dbc->Query($sql);
	$total = $dbc->Fetch($rst);
?>

<div class="row gutters-sm">
	<div class="col-xl-4">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<tbody>
				<tr><th class="text-center">Today kg.</th></tr>
				<tr><td class="text-center"><?php echo $total[0];?></td></tr>
				<tr><th class="text-center">จำนวน Order </th></tr>
				<tr><td class="text-center"><?php echo $total[1];?></td></tr>
			</tbody>
		</table>
		<button onclick="fn.dialog.open('apps/sales_overview/view/dialog.lock.lookup.php','#dialog_lock_lookup')"  type="button" class="btn btn-primary">Lock Report</button>
	</div>
	<div class="col-xl-8">
		<table class="table table-striped table-sm table-bordered dt-responsive nowrap">
			<tbody>
				<tr>
					<th class="text-right">B/f</th>
					<td>2,569.0000</td>
					<td>48,283,154.00</td>
				</tr>
				<tr>
					<th class="text-right">Today Sale</th>
					<td>19.0000</td>
					<td>366,375.30</td>
				</tr>
				<tr>
					<th class="text-right">Today Delivery</th>
					<td>-1,734.0000</td>
					<td>-30,854,805.30</td>
				</tr>
				<tr>
					<th class="text-right">Adjust</th>
					<td>0.000</td>
					<td>0.00</td>
				</tr>
				<tr>
					<th class="text-right">C/f</th>
					<td>854.000</td>
					<td>17,794,724.00</td>
				</tr>
				
			</tbody>
		</table>
	</div>
</div>