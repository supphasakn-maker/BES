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
		<table id="tblImportLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th class="text-center">Select</th>
					<th class="text-center">Date</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">นำหนัก</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$model = array(
					array("type" => "text", "value" => '<span type="checkall" class="far fa-lg fa-square"></span>', "class" => "text-center"),
					array("type" => "date"),
					array("type" => "text", "value" => "Standard", "class" => "text-center"),
					array("type" => "number", "from" => 950, "to" => 2200, "number_format" => 4)
				);
				$demo->loop_table($model, 12);
				?>
			</tbody>
		</table>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-xl");
$modal->setModel("dialog_import_lookup", "Import Lookup");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Select", "")
));
$modal->EchoInterface();

$dbc->Close();
?>