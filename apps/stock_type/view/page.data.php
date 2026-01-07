<?php
	$today = time();

?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*30));?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblSpot").DataTable().draw();'>Lookup</button>
	</form>
</div>

<table id="tblSpot" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_spot" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Purchase</th>
			<th class="text-center">Type</th>
			<th class="text-center">Type</th>
			<th class="text-center">Supplier</th>
			<th class="text-center">Amount</th>
			<th class="text-center">Spot</th>
			<th class="text-center">Pm/Dc</th>
			<th class="text-center">THBValue</th>
			<th class="text-center">User</th>
			<th class="text-center">Ref</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
