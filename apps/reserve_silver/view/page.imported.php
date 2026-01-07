<?php
	global $dbc;
	$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblReserve").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblReserve" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_reserve" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Lock Date</th>
			<th class="text-center">Supplier</th>
			<th class="text-center">Kgs Locked</th>
			<th class="text-center">Already Fixed Rate</th>
			<th class="text-center">To Be Fixed</th>
			<th class="text-center">Locked Disc</th>
			<th class="text-center">Actual Weight</th>
			<th class="text-center">Defer</th>
			<th class="text-center">Bars</th>
			<th class="text-center">Type</th>
			<th class="text-center">Status</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

