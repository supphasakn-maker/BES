<?php
$today = time();
?>

<div class="card mb-2">
<div class="card-body">
<div class="btn-area btn-group mb-2">
<button type="button" class="btn btn-light" onclick='fn.app.stock_silver.silver.dialog_add()'><i class="fas fa-plus"></i>  รับแท่งเข้า</button>
</div>
<table id="tblStockSilver" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_plan" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">หมายเลขแท่ง</th>
			<th class="text-center">PO</th>
			<th class="text-center">ขนาดแท่ง</th>
			<th class="text-center">ประเภทแท่ง</th>
			<th class="text-center">น้ำหนักตามประเภท</th>
			<th class="text-center">วันที่รับเข้า</th>
            <th class="text-center">สถานะ</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tbody>
		<tr>
			<th class="text-right" colspan="5">ยอดรวม</th>
			<th class="text-center" xname="tAmount"></th>
			<th class="text-right" colspan="2"></th>
		</tr>
	</tbody>
</table>
</div>
</div>
