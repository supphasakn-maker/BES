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

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		global $os;
		$prepare = $dbc->GetRecord("bs_stock_prepare", "*", "id=" . $_POST['id']);
		$aPacking = json_decode($os->load_variable("aPacking", "json"), true);
?>
		<form name="form_editpacking">
			<input type="hidden" name="id" value="<?php echo $prepare['id']; ?>">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group row display-group" display-group="daily">
						<div class="col-sm-4">
							<input name="info_amount" class="form-control" placeholder="จำนวนสั่งผลิต" value="<?php echo $prepare['info_amount']; ?>">
						</div>
						<div class="col-sm-4">
							<select name="status_show" class="form-control">
								<option value="1" <?php echo $prepare['status_show'] == 1 ? " selected" : ""; ?>>แสดงที่ห้องผลิต</option>
								<option value="0" <?php echo $prepare['status_show'] == 0 ? " selected" : ""; ?>>ไม่แสดง</option>
							</select>
						</div>
						<div class="col-sm-4">
							<input name="delivery_date" type="date" class="form-control" value="<?php echo $prepare['delivery_date']; ?>">
						</div>
					</div>
					<div class="form-group row display-group" display-group="daily">
						<div class="col-sm-12">
							<input name="info_mine" class="form-control" placeholder="เหมือง" value="<?php echo $prepare['info_mine']; ?>">
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
						<tbody>
							<?php
							$sql = "SELECT * FROM bs_stock_items WHERE prepare_id = " . $prepare['id'];
							$rst = $dbc->Query($sql);
							$total = 0;
							while ($line = $dbc->Fetch($rst)) {

								$fn_calculate = "fn.app.sales.packing.calculation()";
								echo  '<tr class="pack_item">';
								$classname = 'pt-2 text-center item_level';
								echo  '<td xname="number" class="' . $classname . '">#</td>';
								$classname = 'form-control form-control-sm';
								echo  '<td><input xname="name" name="name[]" type="text" class="' . $classname . '" value="' . $line['name'] . '"></td>';
								$classname = 'form-control form-control-sm text-center';
								echo  '<td><input xname="size" name="size[]" type="number" class="' . $classname . '" onchange="' . $fn_calculate . '" value="' . $line['size'] . '"></td>';
								$classname = 'form-control form-control-sm text-center';
								echo  '<td><input xname="amount" name="amount[]" type="number" class="' . $classname . '" onchange="' . $fn_calculate . '" value="' . $line['amount'] . '"></td>';
								$classname = 'form-control-sm form-control text-center';
								echo  '<td><input xname="totaleach" name="totaleach[]" type="text" class="' . $classname . '" readonly value="' . $line['size'] * $line['amount'] . '"></td>';
								$classname = 'form-control-sm form-control text-center';
								echo  '<td><input xname="comment" name="comment[]" type="text" class="' . $classname . '" placeholder="ข้อความพิเศษ" value="' . $line['comment'] . '"></td>';
								$classname = 'btn btn-sm btn-danger';
								echo  '<td class="text-center"><button class="' . $classname . '" onclick="$(this).parent().parent().remove();">ลบ</button></td>';
								echo '</tr>';
								$total += $line['size'] * $line['amount'];
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th class="text-right" colspan="3">ยอดรวม</th>
								<th class="text-center">
									<div class="input-group input-group-sm">
										<input name="total" type="text" class="form-control text-right" readonly value="<?php echo $total; ?>">
										<div class="input-group-append">
											<span class="input-group-text">กิโลกรัม</span>
										</div>
									</div>
								</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</form>

<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_edit_packing", "Save Change");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Packing", "fn.app.sales.packing.edit()")
));
$modal->EchoInterface();

$dbc->Close();
?>