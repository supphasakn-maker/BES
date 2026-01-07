<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/demo.php";
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
		$demo = new demo;
?>

		<form class="form-horizontal">
			<div class="form-group row">
				<label class="col-sm-2 col-form-label text-right">ประเภทถุง</label>
				<div class="col-sm-10">
					<select class="form-control">
						<option>15 กิโลกรัม</option>
						<option>30 กิโลกรัม</option>
						<option>ผสม</option>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label text-right">หมายเลข</label>
				<div class="col-sm-5">
					<input class="form-control" name="round" placeholder="เริ่มต้น">
				</div>
				<div class="col-sm-5">
					<input class="form-control" name="round" placeholder="สิ้นสุด">
				</div>
			</div>
		</form>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-xl");
$modal->setModel("dialog_add_pack", "เพิ่มถุง");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Select", "")
));
$modal->EchoInterface();

$dbc->Close();
?>