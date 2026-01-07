<h2 class="text-center">เศษเสียรอการผลิต</h2>
<table id="tblScrapData" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white">รอบการผลิต</th>
			<th class="text-center text-white">หมายเลขเศษ</th>
			<th class="text-center text-white">วันที่บันทึก</th>
			<th class="text-center text-white">วันที่แก้ไข</th>
			<th class="text-center text-white">ประเภท</th>
			<th class="text-center text-white">PRODUCT</th>
			<th class="text-center text-white">น้ำหนักปัจจุบัน</th>
			<th class="text-center text-white">น้ำหนักจริง</th>
			<th class="text-center text-white">ส่วนต่าง</th>
			<th class="text-center text-white">ACTION</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tbody>
		<tr>
			<th class="text-right" colspan="7">ยอดรวม</th>
			<th class="text-right" xname="tAmount"></th>
			<th class="text-right" colspan="3"></th>
		</tr>
	</tbody>
</table>
<table class="table mt-2 table-bordered">
	<thead>
		<tr>
			<th class="text-center">ประเภท</th>
			<th class="text-center">PRODUCT</th>
			<th class="text-center">จำนวน</th>
			<th class="text-center">Weight</th>
		</tr>
		<thead>
		<tbody>
			<?php
			global $dbc;
			$data = array();
			$sql = "SELECT  
    ANY_VALUE(bs_scrap_items.code) AS code, 
    bs_scrap_items.pack_name AS pack_name,
    ANY_VALUE(bs_scrap_items.product_id) AS product_id,
    bs_products.name AS product_name,
    COUNT(bs_scrap_items.id) AS total_item,
    SUM(bs_scrap_items.weight_expected) AS weight_expected
FROM bs_scrap_items 
LEFT JOIN bs_products ON bs_scrap_items.product_id = bs_products.id
WHERE bs_scrap_items.status > -1 
GROUP BY bs_scrap_items.pack_name, bs_products.name";
			$rst = $dbc->Query($sql);
			while ($item = $dbc->Fetch($rst)) {
				echo '<tr>';
				echo '<td class="text-center">' . $item['pack_name'] . '</td>';
				echo '<td class="text-center">' . $item['product_name'] . '</td>';
				echo '<td class="text-center">' . $item['total_item'] . '</td>';
				echo '<td class="text-center">' . number_format($item['weight_expected'], 4) . '</td>';
				echo '</tr>';
			}
			?>
		</tbody>
</table>
<h2 class="text-center">เศษเสียรอการ Refine</h2>
<div class="btn-area btn-group mb-2">
	<button onclick="fn.app.production_scrap.scrap.dialog_combine()" class="btn btn-warning">Combine</button>
</div>
<table id="tblScrapRefine" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead class="bg-dark">
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_repack" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center text-white">รอบการผลิต</th>
			<th class="text-center text-white">หมายเลขเศษ</th>
			<th class="text-center text-white">วันที่บันทึก</th>
			<th class="text-center text-white">วันที่แก้ไข</th>
			<th class="text-center text-white">ประเภท</th>
			<th class="text-center text-white">PRODUCT</th>
			<th class="text-center text-white">น้ำหนักปัจจุบัน</th>
			<th class="text-center text-white">น้ำหนักจริง</th>
			<th class="text-center text-white">ส่วนต่าง</th>
			<th class="text-center text-white">ACTION</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tbody>
		<tr>
			<th class="text-right" colspan="7">ยอดรวม</th>
			<th class="text-right" xname="tAmount"></th>
			<th class="text-right" colspan="3"></th>
		</tr>
	</tbody>
</table>