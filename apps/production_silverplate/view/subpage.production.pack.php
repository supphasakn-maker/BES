<div class="row">


	<div class="col-6">
		<div class="mb-4">
			<div>
				ผู้รับผิดชอบ
			</div>
			<select class="form-control">
				<option>โปรดเเลือกผู้รับผิดชอบ</option>
				<option>นาย ทองดี ขาวสะอาด</option>
				<option>คนงาน 2</option>
			</select>
		</div>
		<table class="table table-sm table-striped table-bordered dt-responsive nowrap">
			<!-- Filter columns -->
			<thead>
				<tr>
					<th class="text-center">รายการ</th>
					<th class="text-center">จำนวนที่ต้องการ</th>
					<th class="text-center">รวม</th>
					<th class="text-center">หมายุเหตุ</th>
				</tr>
			</thead>
			<!-- /Filter columns -->
			<tbody>
				<tr>
					<td class="text-center">1 กิโลกรัม</th>
					<td class="text-center">20</th>
					<td class="text-center">20</th>
					<td class="text-center">
						</th>
				</tr>
				<tr>
					<td class="text-center">2 กิโลกรัม</th>
					<td class="text-center">24</th>
					<td class="text-center">48</th>
					<td class="text-center">
						</th>
				</tr>
				<tr>
					<td class="text-center">3 กิโลกรัม</th>
					<td class="text-center">20</th>
					<td class="text-center">60</th>
					<td class="text-center">แบ่งแบบพิเศษ</th>
				</tr>
				<tr>
					<td class="text-center">5 กิโลกรัม</th>
					<td class="text-center">1</th>
					<td class="text-center">2</th>
					<td class="text-center">10</th>
				</tr>
				</thead>
			</tbody>
		</table>
	</div>
	<div class="col-6">
		<div class="">
			<a type="button" onclick="fn.dialog.open('apps/production_silverplate/view/dialog.pack.add.php','#dialog_add_pack')" class="btn btn-primary">เพิ่มถุง</a>

		</div>
		<table class="mt-2 table table-bordered">
			<thead>
				<tr>
					<th></th>
					<th class="text-center">หมายเลขถุง</th>
					<th class="text-center">ประเภทถุง</th>
					<th class="text-center">น้ำหนัก</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#" class="btn btn-sm btn-danger">X</a></td>
					<td><input class="form-control"></td>
					<td>
						<select class="form-control">
							<option>ถุงปกติ</option>
							<option>ถุงกระสอบ</option>
						</select>
					</td>
					<td><input class="form-control" placeholder="น้ำหนักที่ชั่งไว้"></td>
				</tr>
				<tr>
					<td><a href="#" class="btn btn-sm btn-danger">X</a></td>
					<td><input class="form-control"></td>
					<td>
						<select class="form-control">
							<option>ถุงปกติ</option>
							<option>ถุงกระสอบ</option>
						</select>
					</td>
					<td><input class="form-control" placeholder="น้ำหนักที่ชั่งไว้"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>