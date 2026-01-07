<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline" onsubmit="return 0;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d");?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d");?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblDelivery").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblPrepare" class="table table-striped table-bordered dt-responsive nowrap">
	<thead>
		<tr>
			<th class="text-center">ประจำวันที่</th>
			<th class="text-center">จำนวนสี่งผลิต</th>
			<th class="text-center">เหมือง</th>
			<th class="text-center">แสดงผล</th>
			<th class="text-center">จำนวนทั้งหมด</th>
			<th class="text-center">สถานะ</th>
			<th class="text-center">แก้ไข</th>
		</tr>
	</thead>
</table>

