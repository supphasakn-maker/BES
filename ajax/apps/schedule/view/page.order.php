<?php
	$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblOrder").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblOrder" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_order" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center"><i class="far fa-sm fa-cut"></i></th>
			<th class="text-center"><i class="far fa-sm fa-pen"></i></th>
			<th class="text-center">หายเลขสั่งซื้อ</th>
			<th class="text-center">หมายเลขส่งของ</th>
			<th class="text-center">ชื่อลูกค้า</th>
			<th class="text-center">จำนวน</th>
			<th class="text-center">ราคา/กิโลกรัม</th>
			<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
			<th class="text-center">ยอดรวม</th>
			<th class="text-center">วันที่สั่งซื้อ</th>
			<th class="text-center">วันที่ส่งของ</th>
			<th class="text-center">เลื่อนวัน</th>
			<th class="text-center">ผู้ขาย</th>
			<th class="text-center" id="schedule_header">
				
			</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
