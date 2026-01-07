<?php
	$today = time();
?>
<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline mr-2" onsubmit="return false;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today);?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",$today+(86400*30));?>">
		<button type="button" class="btn btn-primary" onclick='$("#tblPayment").DataTable().draw();'>Lookup</button>
	</form>
</div>


<table id="tblPayment" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">วันที่ชำระเงิน</th>
			<th class="text-center">วันเวลาเงินเข้าจริง</th>
			<th class="text-center">ลูกค้า</th>
			<th class="text-center">หมายการสั่งซื้อ</th>
			<th class="text-center">จำนวนเงิน</th>
			<th class="text-center">วิธีการชำระเงิน</th>
			<th class="text-center">ธนาคาร</th>
			<th class="text-center">Reference</th>
			<th class="text-center">วิธีรับเงิน</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
