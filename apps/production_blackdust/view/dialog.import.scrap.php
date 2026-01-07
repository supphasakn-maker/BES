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
?>
		<table id="tblImportLookup" data-id="<?php echo $_REQUEST['id']; ?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead class="bg-dark">
				<tr>
					<th class="text-center hidden-xs">
						<span type="checkall" control="chk_import" class="far fa-lg fa-square"></span>
					</th>
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
<?php


	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_import_lookup", "Import Scrap");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Map", "fn.app.production_blackdust.import.dialog_select(" . $_POST['id'] . ")")
));
$modal->EchoInterface();

$dbc->Close();
?>