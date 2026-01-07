

<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col-4">
				<form>
					<table class="table table-bordered table-form">
						<tbody>
							<tr>
								<td><label>รอบที่ </label></td>
								<td><input type="text" class="form-control" value="35"></td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
			<div class="col-2">
				<button onclick="window.history.back();" class="btn btn-danger">Back</button>
			</div>
		</div>
		<h3>1. ใบรับวัตุดิบ (INCOT TRASFER FORM)
		<button type="button" class="btn btn-dark"  onclick="fn.dialog.open('apps/production/view/dialog.import.lookup.php','#dialog_import_lookup')">เลือก</button>
		</h3>
		<table class="table table-sm table-striped table-bordered  nowrap">
			<thead>
				<tr>
					<th class="text-center">วันที่</th>
					<th class="text-center">เวลาเริ่มชั่งแท่งเงิน</th>
					<th class="text-center">เลขที่ใบขน</th>
					<th class="text-center">จำนวนแท่งเข้า</th>
					<th class="text-center">ประเภทแท่ง</th>
					<th class="text-center">น้ำหนักสุทธิ</th>
					<th class="text-center">น้ำหนักที่ชั่ง</th>
					<th class="text-center">น้ำหนักที่เข้าหลอม</th>
					<th class="text-center">น้ำหนักที่เข้าเซฟ</th>
					<th class="text-center">น้ำหนัก ขาด/เกิน</th>
					<th class="text-center">น้ำหนักเฉลี่ย</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>21/08/2020</td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm"></td>
					<td>
						<select class="form-control form-control-sm">
						<option>15 กิโลกรัม</option>
						<option>30 กิโลกรัม</option>
						<option>ผสม</option>
						</select>
					
					</td>
					<td>-</td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td>0.1101</td>
					<td>15.6859</td>
				</tr>
				<tr>
					<td>21/08/2020</td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm"></td>
					<td>
						<select class="form-control form-control-sm">
						<option>15 กิโลกรัม</option>
						<option>30 กิโลกรัม</option>
						<option>ผสม</option>
						</select>
					</td>
					<td>-</td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td><input class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล"></td>
					<td>0.1101</td>
					<td>15.6859</td>
				</tr>
			</tbody>
		</table>
		<form>
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Remark</label></td>
						<td>
							<select class="form-control">
								<option>Brinks</option>
								<option>G4S</option>
								<option> Malca-amit</option>
							</select>
						</td>
						<td><label> เลือกประเภท</label></td>
						<td>
							<select class="form-control">
								<option>แท่ง</option>
								<option>เม็ด</option>
							</select>
						</td>
						<td><label>ประเภทงาน </label></td>
						<td>
							<select class="form-control">
								<option>หลอม</option>
								<option>แพ็ค</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label>กรมศุลกากร</label></td>
						<td>
							<select class="form-control">
								<option>เจาะแท่ง</option>
								<option>ไม่เจาะแท่ง</option>
							</select>
						</td>
					</tr>
					
					
					<tr>
						<td><label>ทะเบียนรถขนส่ง</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>นัดพนักงาน</label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>รถขนส่งมาถึงเวลา</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ลงชื่อผู้เช็คน้ำหนัก</label></td>
						<td>
							<select class="form-control">
								<option>Chaiyut Khongkhunmaung</option>
								<option>Natacha Nuangkhantree </option>
								<option>Nirun Sunmaair</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label>ลงชื่อ (แผนกขนส่ง) </label></td>
						<td>
							<select class="form-control">
								<option>Chaiyut Khongkhunmaung</option>
								<option>Natacha Nuangkhantree </option>
								<option>Nirun Sunmaair</option>
							</select>
						</td>
						<td><label>ลงชื่อผู้ตรวจเช็ค</label></td>
						<td>
							<select class="form-control">
								<option>Chaiyut Khongkhunmaung</option>
								<option>Natacha Nuangkhantree </option>
								<option>Nirun Sunmaair</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<h3>2. SILVER ก่อนเริ่มและหลังจบจากคลัง (BEGINNING AND ENDING GOODS TRANSFER FORM)  </h3>
		<div class="row">
			<div class="col-md-6">
		<form>
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ปะการัง ค้างในเบ้า เตาที่ 1</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ความสูง</label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>อื่นๆ</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ปะการัง ค้างในเบ้า เตาที่ 2</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ความสูง</label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 1</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>อบ ไม่อบ</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label> หมายเหตุ : </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 2</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>ปะการังเก็บเข้าเซฟ </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>รวม นน. เข้าทั้งหมด</label></td>
						<td><input type="text" class="form-control"></td>
						<td><label>อื่นๆ </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td rowspan="3"><label>บันทึก</label></td>
						<td rowspan="3"><textarea class="form-control"></textarea></td>
						<td><label>อนน. Packing รวม </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>รวม นน. ออกทั้งหมด  </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
					<tr>
						<td><label>Processing Loss   </label></td>
						<td><input type="text" class="form-control"></td>
					</tr>
				</tbody>
			</table>
		</form>
			</div>
			<div class="col-md-5">
			
			<table class="table table-striped table-bordered dt-responsive nowrap">
			<!-- Filter columns -->
			<thead>
				<tr>
					<th class="text-center">รายการ</th>
					<th class="text-center">จำนวนที่ต้องการ</th>
					<th class="text-center">แบ่ง</th>
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
					<td class="text-center">20</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">2 กิโลกรัม</th>
					<td class="text-center">24</th>
					<td class="text-center">25</th>
					<td class="text-center">48</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">3 กิโลกรัม</th>
					<td class="text-center">20</th>
					<td class="text-center">30</th>
					<td class="text-center">60</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">5 กิโลกรัม</th>
					<td class="text-center">1</th>
					<td class="text-center">2</th>
					<td class="text-center">10</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
			</thead>	
			</tbody>
		</table>
			
			</div>
			
		</div>
		<button class="btn btn-secondary" type="reset">ยกเลิก</button>
		<button class="btn btn-primary" type="button">ทำรายการ</button>










    
<script>
		var plugins = [
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		]
		App.loadPlugins(plugins, null).then(() => {
			App.checkAll()
			// Run datatable
			var table = $('#example').DataTable({
				drawCallback: function() {
					$('.dataTables_paginate > .pagination').addClass('pagination-sm') // make pagination small
				}
			})
			// Apply column filter
			$('#example .dt-column-filter th').each(function(i) {
				$('input', this).on('keyup change', function() {
					if (table.column(i).search() !== this.value) {
						table
							.column(i)
							.search(this.value)
							.draw()
					}
				})
			})
			// Toggle Column filter function
			var responsiveFilter = function(table, index, val) {
				var th = $(table).find('.dt-column-filter th').eq(index)
				val === true ? th.removeClass('d-none') : th.addClass('d-none')
			}
			// Run Toggle Column filter at first
			$.each(table.columns().responsiveHidden(), function(index, val) {
				responsiveFilter('#example', index, val)
			})
			// Run Toggle Column filter on responsive-resize event
			table.on('responsive-resize', function(e, datatable, columns) {
				$.each(columns, function(index, val) {
					responsiveFilter('#example', index, val)
				})
			})

		}).then(() => App.stopLoading())
	</script>