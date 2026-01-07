<?php
	$today = time();

?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*30));?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblPayment").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblPayment" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_payment" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Delviery Note</th>
			<th class="text-center">Order</th>
			<th class="text-center">Invoice</th>
			<th class="text-center">Type</th>
			<th class="text-center">Delivery Date</th>
			<th class="text-center">Total</th>
			<th class="text-center">VAT</th>
			<th class="text-center">Net</th>
			<th class="text-center">Payment Info</th>
			<th class="text-center">Customer</th>
			<th class="text-center">Status</th>
			<th class="text-center">Payment Status</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>