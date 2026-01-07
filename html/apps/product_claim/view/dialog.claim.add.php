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
			<div class="text-center">
				<form name="form_addgroup" class="form-horizontal" role="form" onsubmit=";return false;">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">ประเภทการแจ้ง</label>
						<div class="col-sm-10">
							<select class="form-control">
								<option>แจ้งเพื่อทราบและปรับปรุง</option>
								<option>แจ้งเพื่อเคลมสินค้า</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">ชื่อบริษัท</label>
						<div class="col-sm-10">
							<select class="form-control">
								<option>A</option>
								<option>B</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">ผู้ติดต่อ</label>
						<div class="col-sm-3">
							<input class="form-control" placeholder="ผู้แจ้ง">
						</div>
						<div class="col-sm-3">
							<input class="form-control" placeholder="ผู้ส่ง">
						</div>
						<div class="col-sm-3">
							<input class="form-control" placeholder="พนักงานขาย">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">ปัญหาที่แจ้ง</label>
						<div class="col-sm-10">
							<select class="form-control">
								<option>เม็ดชื้น</option>
								<option>เป็นผง</option>
								<option>เม็ดเหลือง/ไม่สวย</option>
								<option>แพ็คเก็จไม่สมบูรณ์</option>
								<option>น้ำหนักขาด</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">จำนวนปัญหา</label>
						<div class="col-sm-6">
							<input class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">วันที่ส่ง</label>
						<div class="col-sm-6">
							<input class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">ใส่ขนาดถุงที่พบปัญหา</label>
						<div class="col-sm-6">
							<input class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right"> ใส่จำนวนที่ต้องการเคลมสินค้า</label>
						<div class="col-sm-6">
							<input class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right"> รายละเอียด</label>
						<div class="col-sm-6">
							<textarea class="form-control"></textarea>
						</div>
					</div>
				</form>
				
			</div>
				
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setExtraClass("modal-xl");
	$modal->setModel("dialog_add","เพิ่ม");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>