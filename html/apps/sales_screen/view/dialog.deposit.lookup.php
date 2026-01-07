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
				<table id="tblPurchaseLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="text-center">วันที่</th>
							<th class="text-center">จำนวนตั้งต้น</th>
							<th class="text-center">เข้า</th>
							<th class="text-center">ออก</th>
							<th class="text-center">ฝาก</th>
							<th class="text-center">รวม</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$model = array(
							array("type" => "date","format"=>"d/m/Y","class"=>"text-center"),
							array("type" => "text","value"=>"10,000","class"=>"text-center"),
							array("type" => "text","value"=>"0","class"=>"text-center"),
							array("type" => "text","value"=>"0","class"=>"text-center"),
							array("type" => "text","value"=>"20","class"=>"text-center"),
							array("type" => "text","value"=>"10,200","class"=>"text-center")
						);
						$demo->loop_table($model,12);
					?>
					</tbody>
				</table>
				<div>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">ประจำนวนที่ 2020-12-24</span>
							<span class="input-group-text">จำนวนฝากอยู่</span>
						</div>
						<input type="text" class="form-control" value="20">
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="button">Save</button>
						</div>
					</div>
				</div>
			<?php
		}
	}
	
	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setExtraClass("modal-xl");
	$modal->setModel("dialog_deposit_lookup","ประวัติการสั่งซื้อ");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();
	
	$dbc->Close();
?>