<div class="card">
	<div class="card-header border-0">
		<h6>ประวัติการซื้อ </h6>
	</div>
	
	<div class="card-body">
		<div class="btn-group mb-2">
			<form name="filter" class="form-inline mr-2" onsubmit="return false;">
				<label class="mr-sm-2">From</label>
				<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today-(86400*30));?>">
				<label class="mr-sm-2">To</label>
				<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
				<button type="button" class="btn btn-primary" onclick='$("#tblDailyTable").DataTable().draw();'>Lookup</button>
			</form>
		</div>
		<table id="tblDailyTable" class="datatable table table-striped table-sm table-bordered dt-responsive nowrap">
			<thead>
				<tr>
					<th class="text-center">หมายเลข</th>
					<th class="text-center">วันเวลาที่สั่ง</th>
					<th class="text-center">วันที่ส่ง</th>
					<th class="text-center">กิโล</th>
					<th class="text-center">บาท/กิโลกรัม</th>
					<th class="text-center">ผู้ขาย</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tbody>
				<tr>
					<th class="text-right" colspan="3">ยอดรวม</th>
					<th class="text-center" colspan="3" xname="tAmount"></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>