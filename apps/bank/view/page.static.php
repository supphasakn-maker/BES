<?php
	global $dbc;
?>
<div class="btn-area btn-group mb-2">
</div>
<table id="tblStatic" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_static" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Type</th>
			<th class="text-center">Topic</th>
			<th class="text-center">Customer</th>
			<th class="text-center">Start</th>
			<th class="text-center">End</th>
			<th class="text-center">Amount</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<form style="display:none;" name="import" enctype="multipart/form-data" >
	<input name="file" type="file">
</form>