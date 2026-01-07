<?php
	$today = time();
?>

<div class="btn-area btn-group mb-2">
	<input id="date_filter" type="date" class="form-control" value="<?php echo date("Y-m-d",$today)?>" onchange="$('#tblCOA').DataTable().draw();">
</div>
<table id="tblCOA" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white font-weight-bold">DELIVERY</th>
			<th class="text-center text-white font-weight-bold">ORDER</th>
			<th class="text-center text-white font-weight-bold">CUSTOMER</th>
			<th class="text-center text-white font-weight-bold">DELIVERY DATE</th>
			<th class="text-center text-white font-weight-bold">WEIGHT</th>
			<th class="text-center text-white font-weight-bold">TOTAL ITEM</th>
			<th class="text-center text-white font-weight-bold"></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>