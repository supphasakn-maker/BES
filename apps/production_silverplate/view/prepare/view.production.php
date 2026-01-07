	<div class="row">
		<div class="col-12">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Product</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_products",
									"name" => "name",
									"value" => "id"
								),
								"name" => "product_id",
								"value" => $production['product_id']
							));
							?>
						</td>
					<tr>

				</tbody>
			</table>
		</div>
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>Remark</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "supplier",
								"source" => array(
									"Brinks",
									"G4S",
									"PMR",
									"MALCA-AMIT"
								),
								"value" => $production['supplier']
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
							),
							"value" => $production['type_work']
						));
						?>
					</td>
					<tr>
					</tr>
					<tr>
						<td><label>แท่งเงินใช้หลอม</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_products",
									"name" => "name",
									"value" => "id",
									"where" => "id in (1,3,4,10)"
								),
								"name" => "product_id_out",
								"value" => $production['product_id_out']
							));
							?>
						</td>
					<tr>
						<td><label name="type_material">ประเภทงาน </label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "combobox",
								"name" => "type_material",
								"source" => array(
									"หลอมเม็ดเงิน",
									"หลอมแท่งเงิน",
									"แพ็คเม็ดเงิน",
									"แพ็คแท่งเงิน",
								),
								"value" => $production['type_material']
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
								),
								"value" => $production['type_thaicustoms_method']
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
								"name" => "delivery_license",
								"value" => $production['delivery_license']
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
								"name" => "delivery_time",
								"value" => $production['delivery_time']
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>ลงชื่อ (แผนกขนส่ง) </label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"name" => "delivery_driver",
								"value" => $production['delivery_driver']
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
								"name" => "approver_appointment",
								"value" => $production['approver_appointment']
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
						<td><label>ผู้บันทึกเข้า</label></td>
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
								"multiple" => true,
								"size" => 8,
								"value" => $production['approver_weight']
							));
							?>
						</td>
					</tr>
					<tr>
						<td><label>ผู้บันทึกออก</label></td>
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
								"multiple" => true,
								"size" => 8,
								"value" => $production['approver_general']
							));
							?>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>


	<h3>2. SILVER ก่อนเริ่มและหลังจบจากคลัง (BEGINNING AND ENDING GOODS TRANSFER FORM) </h3>
	<div class="row">
		<div class="col-md-5">
			<table id="form-second-process" class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>เศษเสียรอการผลิต (นำเข้า)</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_safe", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_safe'])); ?></td>
						<td><label>เศษเสียรอการผลิต (นำออก)</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_safe", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_safe'])); ?></td>
					</tr>
					<tr style="display: none;">
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_plate", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_plate'])); ?></td>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_plate", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_plate'])); ?></td>
					</tr>
					<tr style="display: none;">
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_nugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_nugget'])); ?></td>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_nugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_nugget'])); ?></td>
					</tr>
					<tr style="display: none;">
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blacknugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_blacknugget'])); ?></td>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blacknugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_blacknugget'])); ?></td>
					</tr>
					<tr style="display: none;">
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_whitedust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_whitedust'])); ?></td>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_whitedust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_whitedust'])); ?></td>
					</tr>
					<tr style="display: none;">
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blackdust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_blackdust'])); ?></td>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blackdust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_blackdust'])); ?></td>
					</tr>
					<tr>
						<td><label>เศษเสีย รอ (Refine)</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_refine", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_refine'])); ?></td>
						<td><label>เศษเสีย รอ (Refine)</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_refine", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_refine'])); ?></td>
					</tr>
					<tr>
						<td><label>นน.หลอมเม็ดเงิน , แท่งเงิน (เตาแรก)</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_1", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_1'])); ?></td>
						<td><label>นน. Packing รวม</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_packing", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_packing'])); ?></td>
					</tr>
					<tr>
						<td><label>นน.หลอมแท่งเงิน</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_2", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_2'])); ?></td>
						<td class="bg-warning"><label>รวม นน. ออกทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_total", "value" => 0.00, "class" => "text-right", "readonly" => true, "value" => $production['weight_out_total'])); ?></td>
					</tr>
					<tr>
						<td><label>นน.หลอมเม็ดเงิน</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_3", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_3'])); ?></td>
						<td class="bg-danger"><label>Processing Loss</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_margin", "value" => 0.00, "class" => "text-right", "readonly" => true, "value" => $production['weight_margin'])); ?></td>
					</tr>
					<tr>
						<td><label>นน.แพ็คเม็ดเงิน , แท่งเงิน</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_4", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_4'])); ?></td>
						<td rowspan="2"><label>Remark</label></td>
						<td rowspan="2"><?php $ui_form->EchoItem(array("name" => "remark", "type" => "textarea", "value" => $production['remark'])); ?></td>
					</tr>
					<tr>
						<td class="bg-warning"><label>รวม นน. เข้าทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_total", "value" => 0.00, "class" => "text-right", "readonly" => true, "value" => $production['weight_in_total'])); ?></td>
					</tr>
				</tbody>
			</table>
			<a type="button" onclick="fn.app.production_silverplate.prepare.calculater()" class="btn btn-danger">Calculate Packing</a>

		</div>
		<div id="Output2" class="col-md-7">

		</div>
		</table>
	</div>
	<h3>3.จัดการเศษ</h3>
	<div class="row">
		<div class="col-12">
			<div class="mb-2">
				<button class="btn btn-warning" type="button" onclick="fn.app.production_silverplate.import.dialog_lookup(<?php echo $production['id']; ?>);">เพิ่มเศษ (นำเข้า)</button>
				<button class="btn btn-primary" type="button" onclick="fn.app.production_silverplate.prepare.dialog_add_scrap(<?php echo $production['id']; ?>);">เพิ่มเศษ (นำออก)</button>
			</div>
			<table id="tblScrap" data-id="<?php echo $production['id']; ?>" class="table table-striped table-bordered table-hover table-middle" width="100%">
				<thead class="bg-dark">
					<tr>
						<th class="text-center text-white">รอบการผลิต</th>
						<th class="text-center text-white">หมายเลขเศษ</th>
						<th class="text-center text-white">แบ่ง</th>
						<th class="text-center text-white">วันที่บันทึก</th>
						<th class="text-center text-white">ประเภท</th>
						<th class="text-center text-white">น้ำหนัก</th>
						<th class="text-center text-white">PRODUCT</th>
						<th class="text-center text-white">ACTION</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<h3>4. รายละเอียดการนำเข้า </h3>
	<div class="row">
		<div class="mb-2">
			<!-- <button class="btn btn-primary" type="button" onclick="fn.app.production_silverplate.prepare.append_import();">Append</button> -->
			<button class="btn btn-primary" type="button" onclick="fn.app.production_silverplate.prepare.append_import(null,true);">Append</button>
		</div>
		<table id="tblImport" class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">เวลาเริ่มชั่งแท่งเงิน</th>
					<th class="text-center">เลขที่ใบขน</th>
					<th class="text-center">จำนวนแท่งเข้า</th>
					<th class="text-center">น้ำหนักสุทธิ</th>
					<th class="text-center">น้ำหนักที่ชั่ง</th>
					<th class="text-center">น้ำหนัก ขาด/เกิน</th>
					<th class="text-center">น้ำหนักเฉลี่ย</th>
				</tr>
				<thead>
				<tbody>
					<?php
					if (isset($production['data']))
						if ($production['data'] != null) {
							$data = json_decode($production['data'], true);
							foreach ($data['import'] as $import) {
								$onChange = "fn.app.production_silverplate.prepare.calcuate_import()";
								echo '';
								echo '<tr>';
								echo '<td><input type="time" value="' . $import['import_time'] . '" name="import_time[]" class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_number'] . '" name="import_number[]" class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_bar'] . '" onchange="' . $onChange . '" xname="import_bar" name="import_bar[]" class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_weight_in'] . '" onchange="' . $onChange . '" xname="import_weight_in" name="import_weight_in[]" class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_weight_actual'] . '" onchange="' . $onChange . '" xname="import_weight_actual" name="import_weight_actual[]" class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_weight_margin'] . '" xname="import_weight_margin" name="import_weight_margin[]" readonly class="form-control text-center"></td>';
								echo '<td><input type="text" value="' . $import['import_weight_bar'] . '" xname="import_weight_bar" name="import_weight_bar[]" readonly class="form-control text-center"></td>';
								echo '<td><span class="btn btn-xs btn-danger" onclick="$(this).parent().parent().remove()">Remove</span></td>';
								echo '';
								echo '</tr>';
							}
						}

					?>
				</tbody>
		</table>
	</div>