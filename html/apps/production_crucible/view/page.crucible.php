<?php
$today = time();
?>
<div class="btn-area btn-group mb-2">
<form name="filter" class="form-inline" onsubmit="return 0;">
	<label class="mr-sm-2">From</label>
	<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*90));?>">
	<label class="mr-sm-2">To</label>
	<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
	<button type="button" class="btn btn-primary mr-2" onclick='$("#tblCrucible").DataTable().draw();'>Lookup</button>
</form>
</div>
<table id="tblCrucible" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white">วันที่เพิ่มเบ้าในระบบ</th>
			<th class="text-center text-white">หมายเลขเบ้า</th>
			<th class="text-center text-white">วันที่ใช้เบ้า</th>
			<th class="text-center text-white">วันที่ทุบเบ้า</th>
			<th class="text-center text-white">ACTION</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>