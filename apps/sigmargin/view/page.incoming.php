<?php
	$today = time();
?>
<div class="mb-2 float-right">
	<input id="selcted_date" type="date" class="form-control" value="<?php echo date("Y-m-d",$today)?>" onchange="$('#tblIncoming').DataTable().draw();">
</div>
<div class="btn-area btn-group mb-2">
</div>
<table id="tblIncoming" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_incoming" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">KGS</th>
			<th class="text-center">Pm / Dc</th>
			<th class="text-center">Value Date</th>
			<th class="text-center">ค่าสินค้า</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
