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
		$import = $dbc->GetRecord("bs_import_usd_splited", "*", "id=" . $this->param['id']);

		echo "รายการต่อไปนี้จะถูกลบด้วย";
		$sql = "SELECT * FROM bs_import_usd_splited WHERE import_id = " . $import['import_id'];
		$rst = $dbc->Query($sql);
		while ($line = $dbc->Fetch($rst)) {
			echo '<div>';
			echo '<span class="badge badge-primary">' . $line['id'] . ' </span>';
			echo number_format($line['amount'], 2);
			echo '<span class="badge badge-dark"> ' . $line['created'] . '</span>';
			echo '</div>';
		}
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_remove_import", "Remove Combine");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Remove", "fn.app.import_combine.combine.remove(" . $_POST['id'] . ")")
));
$modal->EchoInterface();

$dbc->Close();
