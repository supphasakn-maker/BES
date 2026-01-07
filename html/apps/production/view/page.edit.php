<?php
global $ui_form, $dbc;
$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_GET['id']);
?>
<form name="form_editproduce" onsubmit="return false;">
	<input type="hidden" name="id" value="<?php echo $production['id']; ?>">
	<div class="row">
		<div class="col-2">
			<button onclick="window.history.back();" class="btn btn-danger">Back</button>
		</div>
		<div class="col-4">
			<table class="table table-bordered table-form">
				<tbody>
					<tr>
						<td><label>รอบที่ </label></td>
						<td><input readonly name="round" type="text" class="form-control" value="<?php echo $production['round']; ?>"></td>
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
			<?php
			$sql = "SELECT * FROM bs_imports WHERE production_id = " . $production['id'];
			$rst = $dbc->Query($sql);
			while ($import = $dbc->Fetch($rst)) {
				echo '<tr>';
				echo '<td class="text-center">';
				echo '<span class="btn btn-danger btn-xs" onclick="$(this).parent().parent().remove()"><i class="fa fa-trash"></i></span>';
				echo '<input type="hidden" name="import_id[]" value="' . $import['id'] . '">';
				echo '</td>';
				echo '<td class="text-center" width="100">';
				echo $import['delivery_date'];
				echo '</td>';
				echo '<td>';
				echo '<input type="time" name="import_time[]" class="form-control form-control-sm" value="' . $import['delivery_time'] . '" placeholder="ต้องระบุข้อมูล">';
				echo '</td>';
				echo '<td>';
				echo '<input name="import_delivery_note[]" class="form-control form-control-sm" value="' . $import['delivery_note'] . '" placeholder="ต้องระบุข้อมูล">';
				echo '</td>';
				echo '<td>';
				echo '<input xname="bar" onchange="fn.app.production.import.calculate()" value="' . $import['bar'] . '" name="import_bar[]" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
				echo '</td>';
				echo '<td class="text-right">';
				echo '<input xname="amount" name="import_amount[]" value="' . $import['weight_in'] . '" class="form-control form-control-sm" placeholder="ต้องระบุข้อมูล">';
				echo '</td>';
				echo '<td>';
				echo '<input xname="weight_in" onchange="fn.app.production.import.calculate()" value="' . $import['weight_actual'] . '" data-item="id" name="import_weight_in[]" class="form-control form-control-sm text-right">';
				echo '</td>';
				echo '<td class="text-right">';
				echo '<input xname="margin" name="import_weight_margin[]" class="form-control form-control-sm text-right" value="' . $import['weight_margin'] . '" readonly>';
				echo '</td>';
				echo '<td class="text-right">';
				echo '<input xname="average" name="import_weight_average[]" class="form-control form-control-sm text-right" value="' . $import['weight_bar'] . '" readonly>';
				echo '</td>';
				echo '</tr>';
			}
			?>
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
								),
								"value" => $production['remark']
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
					<td><label name="type_material">ประเภทงาน </label></td>
					<td>
						<?php
						$ui_form->EchoItem(array(
							"type" => "combobox",
							"name" => "type_material",
							"source" => array(
								"หลอม",
								"แพ็ค"
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
						<td><label>ลงชื่อผู้เช็คน้ำหนัก</label></td>
						<td>
							<?php
							$ui_form->EchoItem(array(
								"type" => "comboboxdb",
								"source" => array(
									"table" => "bs_employees",
									"name" => "fullname",
									"value" => "id"
								),
								"name" => "approver_weight[]",
								"multiple" => true,
								"size" => 3,
								"value" => $production['approver_weight']
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
									"value" => "id"
								),
								"name" => "approver_general[]",
								"multiple" => true,
								"size" => 3,
								"value" => $production['approver_general']
							));
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id="img_frame" class="row">
				<p class="text-center pl-2 m-2"><a href="javascript:;" onclick="$('form[name=form_uploader] input[name=file]').click();"><i class="fas fa-plus fa-2x"></a></i></p>
				<?php

				if (!is_null($production['imgs'])) {
					$imgs = json_decode($production['imgs'], true);
					//var_dump($imgs);
					foreach ($imgs as $img) {
						$id = rand(0, 999999);
						echo '<a data-toggle="tooltip" data-placement="top" id="' . $id . '" title="' . $img['desc'] . '" onclick="fn.app.production.produce.dialog_file(\'#' . $id . '\')" href="javascript:;" class="m-2">';
						echo '<input type="hidden" xname="img_path" name="img_path[]" value="' . $img['path'] . '">';
						echo '<input type="hidden" xname="img_desc" name="img_desc[]" value="' . $img['desc'] . '">';
						echo '<img style="height:40px;" class="img-thumbnail" src="' . $img['path'] . '">';
						echo '</a>';
					}
				}
				?>

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
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_safe", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_safe'])); ?></td>
						<td><label>ปะการังออกจากเซฟ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_safe", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_safe'])); ?></td>
					</tr>
					<tr>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_plate", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_plate'])); ?></td>
						<td><label>น้ำหนักเบ้า</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_plate", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_plate'])); ?></td>
					</tr>
					<tr>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_nugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_nugget'])); ?></td>
						<td><label>เศษ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_nugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_nugget'])); ?></td>
					</tr>
					<tr>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blacknugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_blacknugget'])); ?></td>
						<td><label>เศษดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blacknugget", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_blacknugget'])); ?></td>
					</tr>
					<tr>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_whitedust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_whitedust'])); ?></td>
						<td><label>ผงขาว</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_whitedust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_whitedust'])); ?></td>
					</tr>
					<tr>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_blackdust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_blackdust'])); ?></td>
						<td><label>ผงดำ</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_blackdust", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_blackdust'])); ?></td>
					</tr>
					<tr>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_refine", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_refine'])); ?></td>
						<td><label>Refine</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_refine", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_refine'])); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 1</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_1", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_1'])); ?></td>
						<td><label>นน. Packing รวม</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_packing", "value" => 0.00, "class" => "text-right", "value" => $production['weight_out_packing'])); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 2</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_2", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_2'])); ?></td>
						<td class="bg-warning"><label>รวม นน. ออกทั้งหมด</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_out_total", "value" => 0.00, "class" => "text-right", "readonly" => true, "value" => $production['weight_out_total'])); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 3</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_in_3", "value" => 0.00, "class" => "text-right", "value" => $production['weight_in_3'])); ?></td>
						<td class="bg-danger"><label>Processing Loss</label></td>
						<td><?php $ui_form->EchoItem(array("name" => "weight_margin", "value" => 0.00, "class" => "text-right", "readonly" => true, "value" => $production['weight_margin'])); ?></td>
					</tr>
					<tr>
						<td><label>นน. เงินเข้า-รอบ 4</label></td>
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

		</div>
		<div class="col-md-5">




			<button onclick="window.history.back()" class="btn btn-secondary" type="button">ยกเลิก</button>
			<button class="btn btn-primary" onclick="fn.app.production.produce.edit();" type="submit">ทำรายการ</button>
		</div>
</form>
<form name="form_uploader" style="display:none;">
	<input type="file" name="file">
</form>