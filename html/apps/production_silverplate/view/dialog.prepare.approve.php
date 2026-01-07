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
		$production = $dbc->GetRecord("bs_productions", "*", "id=" . $this->param['id']);
		echo '<form name="form_approveprepare">';
		echo '<input type="hidden" name="id" value="' . $production['id'] . '">';
		echo '<table class="table table-bordered table-form">';
		echo '<tbody>';
		echo '<tr>';
		echo '<td><label>เวลาที่ submited</label></td>';
		echo '<td><input name="submited" type="datetime-local" class="form-control" value="' . date("Y-m-d H:i:s") . '"></td>';
		echo '</tr>';
		echo '</tbody>';
		echo '</table>';
		echo '</fomr>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_approve_prepare", "Approve Prepare");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Approve", "fn.app.production_silverplate.prepare.approve()")
));
$modal->EchoInterface();

$dbc->Close();
