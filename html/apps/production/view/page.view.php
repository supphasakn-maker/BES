<?php
$today = time();
?><div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*30));?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblProduce").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblProduce" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_produce" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Created</th>
			<th class="text-center">Submited</th>
			<th class="text-center">รอบที่</th>
			<th class="text-center">จำนวนแท่ง</th>
			<th class="text-center">หน่วย</th>
			<th class="text-center">น้ำหนักแท่ง</th>
			<th class="text-center">น้ำหนักใบขน</th>
			<th class="text-center">น้ำหนักเข้าหลอม</th>
			<th class="text-center">น้ำหนักขาดเกิน</th>
			<th class="text-center">จากเซฟ</th>
			<th class="text-center">อื่น ๆ</th>
			<th class="text-center">ผลต่าง</th>
			<th class="text-center">เข้าเซฟ</th>
			<th class="text-center">อื่น ๆ</th>
			<th class="text-center">Action</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
