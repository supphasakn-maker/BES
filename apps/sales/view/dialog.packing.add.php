<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$aPacking = json_decode($os->load_variable("aPacking", "json"), true);

class myModel extends imodal
{
	function body()
	{
		global $aPacking;
		$dbc = $this->dbc;

		/*
			
			if($dbc->hasRecord("bs_stock_prepare","status = 0")){
				echo '<h3>ไม่สามารถเพิ่มรายการได้จนกว่าจะปิด Case</h3>';
				
			}else{
			*/
?>
		<form name="form_addpacking">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group row display-group" display-group="daily">
						<label class="col-sm-2 col-form-label text-right">วันที่</label>
						<div class="col-sm-4">
							<input name="delivery_date_from" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
						</div>
						<label class="col-sm-2 col-form-label text-right">ถึงวันที่</label>
						<div class="col-sm-4">
							<input name="delivery_date_to" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
						</div>
					</div>
					<hr>
					<table id="tblPackAdding" class="mt-3 table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<thead>
								<tr>
									<th class="text-center">รายการ</th>
									<th class="text-center">ขนาดถุง (กก)</th>
									<th class="text-center">จำนวนที่ต้องการ</th>
									<th class="text-center">รวมกิโลกรัม</th>
									<th class="text-center">หมายเหตุ</th>
								</tr>
							</thead>
						</thead>
						<tfoot>
							<tr>
								<th class="text-right" colspan="3">ยอดรวม</th>
								<th class="text-center">
									<div class="input-group input-group-sm">
										<input name="total" type="text" class="form-control text-right" readonly value="0">
										<div class="input-group-append">
											<span class="input-group-text">กิโลกรัม</span>
										</div>
									</div>
								</th>
								<th></th>
							</tr>

						</tfoot>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="col-sm-6">
					<div class="form-group row display-group" display-group="daily">

						<div class="col-sm-4">
							<input name="info_amount" class="form-control" placeholder="จำนวนสั่งผลิต">
						</div>
						<div class="col-sm-4">
							<select name="status_show" class="form-control">
								<option value="1">แสดงที่ห้องผลิต</option>
								<option value="0">ไม่แสดง</option>
							</select>
						</div>
						<div class="col-sm-4">
							<input name="date" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>">
						</div>
					</div>
					<div class="form-group row display-group" display-group="daily">

						<div class="col-sm-12">
							<input name="info_mine" class="form-control" placeholder="เหมือง">
						</div>
					</div>

					<hr>
					<div class="form-inline">
						<select name="packtype" class="form-control mr-2">
							<?php
							foreach ($aPacking as $pack) {
								$readonly = isset($pack['readonly']) ? $pack['readonly'] : true;

								echo '<option data-value="' . $pack['value'] . '" data-readonly="' . ($readonly ? "true" : "false") . '">' . $pack['name'] . '</option>';
							}
							?>
						</select>
						<a id="addpack" class="btn btn-primary" href="javascript:;" onclick="fn.app.sales.packing.packing_append()">เพิ่มถุง</a>
					</div>
					<table id="tblPackNote" class="mt-3 table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<thead>
								<tr>
									<th class="text-center">ลำดับ</th>
									<th class="text-center">รายการ</th>
									<th class="text-center">ขนาด</th>
									<th class="text-center">จำนวนที่สั่งผลิต</th>
									<th class="text-center">รวมกิโลกรัม</th>
									<th class="text-center">หมายเหตุ</th>
									<th class="text-center">ดำเนินการ</th>
								</tr>
							</thead>
						</thead>
						<tfoot>
							<tr>
								<th class="text-right" colspan="3">ยอดรวม</th>
								<th class="text-center">
									<div class="input-group input-group-sm">
										<input name="total" type="text" class="form-control text-right" readonly value="0">
										<div class="input-group-append">
											<span class="input-group-text">กิโลกรัม</span>
										</div>
									</div>
								</th>
								<th></th>
							</tr>
						</tfoot>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>




		</form>

<?php
		/*
			}
			*/
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_add_packing", "สรุปการแบ่งแพ็ค");
$modal->setExtraClass("modal-full");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Packing", "fn.app.sales.packing.add()")
));
$modal->EchoInterface();

$dbc->Close();
?>