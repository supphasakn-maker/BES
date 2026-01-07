<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	include_once "../../../include/demo.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$demo = new demo;
			?>
			<form>
				<div class="form-group row display-group" display-group="daily">
					<label class="col-sm-2 col-form-label text-right">ประจำวันที่</label>
					<div class="col-sm-10">
						<input type="date" class="form-control" value="2020-11-01">
					</div>
				</div>
			</form>
			<form class="form-inline mb-2">
				<select name="packtype" class="form-control mr-2">
					<option value="1">ถุงทั่วไป</option>
					<option value="2">ถุงแบบพิเศษ</option>
				</select>
				<select name="packsize" class="form-control mr-2">
					<option value="1">1 กิโลกรัม</option>
					<option value="2">2 กิโลกรัม</option>
					<option value="3">3 กิโลกรัม</option>
					<option value="5">5 กิโลกรัม</option>
					<option value="10">10 กิโลกรัม</option>
				</select>
				<select name="packcustom" class="form-control mr-2">
					<option>เน้นนำหนัก + เขียนชื่อหน้ากระสอบ</option>
					<option>ไม่เอาผง + เขียนชื่อหน้ากระสอบ</option>
					<option>คัดเม็ดเล้กสวย + เขียนชื่อหน้ากระสอบ</option>
					<option>เขียน lot# ปัจจจุบัน + เขียนชื่อหน้ากระสอบ</option>
					<option>เม็ดใหญ่ + เขียนชื่อหน้ากระสอ</option>
					<option>เอาถุงละ 1x10 ถุงใน 1 กระสอบ + เขียนชื่อหน้ากระสอบ</option>
					<option>เอาถุงละ 2x5 ถุงใน 1 กระสอบ + เขียนชื่อหน้ากระสอบ</option>
					<option>เอาถุงละ 1x5 ถุงใน 1 กระสอบ + เขียนชื่อหน้ากระสอบ</option>
					<option>อื่น ๆ</option>
				</select>
				<a id="addpack" class="btn btn-primary" href="javascript:;">เพิ่มถุง</a>
			</form>
			<table id="example" class="table table-striped table-bordered dt-responsive nowrap">
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
					<td class="text-center"><input class="form-control form-control-sm" value="20"></th>
					<td class="text-center">20</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">2 กิโลกรัม</th>
					<td class="text-center">24</th>
					<td class="text-center"><input class="form-control form-control-sm" value="24"></th>
					<td class="text-center">48</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">3 กิโลกรัม</th>
					<td class="text-center">20</th>
					<td class="text-center"><input class="form-control form-control-sm" value="20"></th>
					<td class="text-center">60</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
				<tr>
					<td class="text-center">5 กิโลกรัม</th>
					<td class="text-center">1</th>
					<td class="text-center"><input class="form-control form-control-sm" value="1"></th>
					<td class="text-center">5</th>
					<td class="text-center"><input class="form-control form-control-sm" placeholder="หมายเหตุ"></th>
				</tr>
			</thead>	
			</tbody>
		</table>
			
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setExtraClass("modal-xl");
	$modal->setModel("dialog_edit","แก้ไข");
	$modal->setButton(array(
		array("close","btn-danger","แก้ไข"),
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>