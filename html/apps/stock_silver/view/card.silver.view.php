<?php
$today = time();
?>

<div class="card mb-2">
<div class="card-body">
<div class="btn-area btn-group mb-2">
<button type="button" class="btn btn-light" onclick='fn.app.stock_silver.scrap.dialog_add()'><i class="fas fa-plus"></i> รับเข้า</button>
</div>
<table id="tblStock" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center">หมายเลข</th>
			<th class="text-center">CODE</th>
			<th class="text-center">ขนาด</th>
			<th class="text-center">ประเภท</th>
			<th class="text-center">น้ำหนักตามประเภท</th>
			<th class="text-center">วันที่รับเข้า</th>
            <th class="text-center">สถานะ</th>
		</tr>
	</thead>
	<tbody>
	</tbody>

</table>
</div>
</div>
