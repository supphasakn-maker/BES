<?php
	$today = time();
?>
<div class="mb-2 float-right">
	<input id="selcted_date" type="date" class="form-control" value="<?php echo date("Y-m-d",$today)?>" onchange="$('#tblSilver').DataTable().draw();">
</div>
<div class="btn-area btn-group mb-2">
</div>

<table id="tblSilver" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_silver" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Physical/Trade</th>
			<th class="text-center">Buy/Sell</th>
			<th class="text-center">Amount</th>
			<th class="text-center">SPOT</th>
			<!-- <th class="text-center">Pm/Dc</th> -->
			<th class="text-center">Value Date</th>
			<th class="text-center">Action</th>
			<th class="text-center">USD Debit</th>
			<th class="text-center">USD Credit</th>
			<th class="text-center">Amount Debit</th>
			<th class="text-center">Amount Credit</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
