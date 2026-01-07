<?php
global $ui_form;
?>
<form name="form_addproduce" onsubmit="fn.app.production.produce.add();return false;">
	<div class="row">
		<div class="col-2">
			<button onclick="window.history.back();" class="btn btn-danger">Back</button>
		</div>
		<div class="col-4">

			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>รอบที่ </label></td>
						<td><input name="round" type="text" class="form-control" value="1"></td>
					</tr>
				</tbody>
			</table>

		</div>

	</div>
	<h3>1. ใบรับวัตุดิบ (INCOT TRASFER FORM)
		<button type="button" class="btn btn-dark" onclick="fn.app.production.import.dialog_lookup(null,true)">เลือก</button>
	</h3>
	<table id="tblImportInput" class="table table-sm table-striped table-bordered">
		<thead>
			<tr>
				<th class="text-center"></th>
				<th class="text-center">วันที่</th>
				<th class="text-center">เวลาเริ่มชั่งแท่งเงิน</th>
				<th class="text-center">เลขที่ใบขน</th>
				<th class="text-center">จำนวนแท่งเข้า</th>
				<th class="text-center">น้ำหนักสุทธิ</th>
				<th class="text-center">น้ำหนักที่ชั่ง</th>
				<th class="text-center">น้ำหนัก ขาด/เกิน</th>
				<th class="text-center">น้ำหนักเฉลี่ย</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div class="row">
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Remark</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "remark",
								"source" => array(
									"Brinks",
									"G4S",
									"PMR",
									"MALCA-AMIT"
								)
							));
							?>
						</td>
					<tr>
					</tr>
					<td><label name="type_work"> เลือกประเภท</label></td>
					<td>
						<?php
						$ui_form->EchoItem(array(
							"type" => "combobox",
							"name" => "type_work",
							"source" => array(
								"แท่ง",
								"เม็ด"
							)
						));
						?>
					</td>
					<tr>
					</tr>
					<td><label name="type_material">ประเภทงาน </label></td>
					<td>
						<?php
						$ui_form->EchoItem(array(
							"type" => "combobox",
							"name" => "type_material",
							"source" => array(
								"หลอม",
								"แพ็ค"
							)
						));
						?>
					</td>
					</tr>
					<tr>
						<td><label>กรมศุลกากร</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "type_thaicustoms_method",
								"source" => array(
									"เจาะแท่ง",
									"ไม่เจาะแท่ง"
								)
							));
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ทะเบียนรถขนส่ง</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "delivery_license"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>รถขนส่งมาถึงเวลา</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "time",
								"name" => "delivery_time"
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>ลงชื่อ (แผนกขนส่ง) </label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "delivery_driver"
							));
							?>
						</td>

					</tr>
					<tr>
						<td><label>นัดพนักงาน</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "time",
								"name" => "approver_appointment"
							));
							?>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-md-4">
			<table class="table table-bordered table-form">
				<tbody>

					<tr>
						<td><label>ลงชื่อผู้เช็คน้ำหนัก</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_employees",
									"name" => "fullname",
									"value" => "id",
									"where" => "department = 3"
								),
								"name" => "approver_weight[]",
								"size" => 8,
								"multiple" => true
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>ลงชื่อผู้ตรวจเช็ค</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_employees",
									"name" => "fullname",
									"value" => "id",
									"where" => "department = 3"
								),
								"name" => "approver_general[]",
								"size" => 8,
								"multiple" => true
							));
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="img_frame" class="row">
				<p class="text-center pl-2 m-2"><a href="javascript:;" onclick="$('form[name=form_uploader] input[name=file]').click();"><i class="fas fa-plus fa-8x"></a></i></p>
			</div>
		</div>

	</div>

	<h3>2. SILVER ก่อนเริ่มและหลังจบจากคลัง (BEGINNING AND ENDING GOODS TRANSFER FORM) </h3>
	<div class="row">
		<div class="col-md-6">
			<table id="form-second-process" class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_safe", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_safe", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_plate", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_plate", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_nugget", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_nugget", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blacknugget", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blacknugget", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_whitedust", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_whitedust", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blackdust", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blackdust", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_refine", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_refine", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 1</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_1", "value" => 0.00, "class" => "text-right")); ?></td>
						<td><label>นน. Packing รวม</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_packing", "value" => 0.00, "class" => "text-right")); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 2</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_2", "value" => 0.00, "class" => "text-right")); ?></td>
						<td class="bg-warning"><label>รวม นน. ออกทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_total", "value" => 0.00, "class" => "text-right", "readonly" => true)); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 3</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_3", "value" => 0.00, "class" => "text-right")); ?></td>
						<td class="bg-danger"><label>Processing Loss</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_margin", "value" => 0.00, "class" => "text-right", "readonly" => true)); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 4</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_4", "value" => 0.00, "class" => "text-right")); ?></td>
						<td rowspan="2"><label>Remark</label></td>
						<td rowspan="2"><?php $ui_form->EchoItem(array("name" => "remark", "type" => "textarea")); ?></td>
					</tr>
					<tr>
						<td class="bg-warning"><label>รวม นน. เข้าทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_total", "value" => 0.00, "class" => "text-right", "readonly" => true)); ?></td>
					</tr>
					<tr>

					</tr>
				</tbody>
			</table>

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


			<button class="btn btn-secondary" type="reset">ยกเลิก</button>
			<button class="btn btn-primary" type="submit">ทำรายการ</button>
</form>

<form name="form_uploader" style="display:none;">
	<input type="file" name="file">
</form>