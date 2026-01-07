<?php
$today = time();
?>

<div class="card mb-2">
<div class="card-body">
<div class="btn-area btn-group mb-2">
<button type="button" class="btn btn-secondary" onclick='fn.app.stock_bwd.silver.dialog_add()'><i class="fas fa-plus"></i>  เพิ่มแท่ง</button>
</div>
<table id="tblStockSilver" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white font-weight">หมายเลขแท่ง</th>
			<th class="text-center text-white font-weight">ขนาด</th>
			<th class="text-center text-white font-weight">ประเภทแท่ง</th>
			<th class="text-center text-white font-weight">น้ำหนัก/กรัม</th>
			<th class="text-center text-white font-weight">Product</th>
			<th class="text-center text-white font-weight">Type</th>
			<th class="text-center text-white font-weight">วันที่รับเข้า</th>
			<th class="text-center text-white font-weight"></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tbody>
		<tr>
			<th class="text-right" colspan="3">ยอดรวม</th>
			<th class="text-center" xname="tAmount"></th>
			<th class="text-right" colspan="4"></th>
		</tr>
	</tbody>
</table>
</div>
</div>
