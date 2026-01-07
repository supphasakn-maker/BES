<div class="btn-area btn-group mb-2">
	<form name="filter" class="form-inline" onsubmit="return 0;">
		<label class="mr-sm-2">From</label>
		<input name="from" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d");?>">
		<label class="mr-sm-2">To</label>
		<input name="to" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d",time()+(7*86400));?>">
		<button type="button" class="btn btn-primary mr-2" onclick='$("#tblDelivery").DataTable().draw();'>Lookup</button>
	</form>
</div>
<table id="tblDelivery" class="table table-striped table-bordered table-hover table-middle" width="100%">
	<thead>
		<tr>
			<th class="text-center hidden-xs">
				<span type="checkall" control="chk_delivery" class="far fa-lg fa-square"></span>
			</th>
			<th class="text-center">Delivery No.</th>
			<th class="text-center">Order No.</th>
			<th class="text-center">ประเภท</th>
			<th class="text-center">ชื่อลูกค้า</th>
			<th class="text-center">กิโล</th>
			<th class="text-center">ราคาขาย</th>
			<th class="text-center">ราคาขาย + VAT 7%</th>
			<th class="text-center">วันที่สั่งซื้อ</th>
			<th class="text-center">วันที่ส่ง</th>
			<th class="text-center">แบ่งแพ็ค</th>
			<th class="text-center">เงื่อนไขการชำระเงิน</th>
			<th class="text-center">บิล</th>
			<th class="text-center">สถานะ</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<!-- <button type="button" class="btn btn-warning"  onclick="fn.app.sales.delivery.dialog_combine()">รวม Order</button> -->
