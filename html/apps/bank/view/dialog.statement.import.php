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
		$table = $this->param['table'];

		echo '<form name="form_importstatement" class="form-horizontal">';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">Bank</label>';
		echo '<div class="col-sm-10">';
		echo '<select class="form-control" name="bank_id">';
		$sql = "SELECT * FROM bs_banks";
		$rst = $dbc->Query($sql);
		while ($bank = $dbc->Fetch($rst)) {
			echo '<option value="' . $bank['id'] . '">' . $bank['name'] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">ข้อมูล</label>';
		echo '<div class="col-sm-10">';
		echo '<input readonly class="form-control" value="' . (count($table) - 1) . ' รายการ">';
		echo '</div>';
		echo '</div>';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">Start</label>';
		echo '<div class="col-sm-10">';
		echo '<input class="form-control" name="start" value="2">';
		echo '</div>';
		echo '</div>';


		echo '<table style="display:none" class="table table-sm">';
		echo '<body>';
		for ($i = 0; $i < count($table); $i++) {
			echo '<tr>';
			for ($j = 0; $j < count($table[$i]); $j++) {
				echo '<td>' . $table[$i][$j] . '</td>';
				echo '<input type="hidden" name="table[' . $i . '][' . $j . ']" value="' . $table[$i][$j] . '">';
			}
			echo '</tr>';
		}
		echo '</body>';
		echo '</table>';

		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_import_statement", "Import statement");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Import", "fn.app.bank.statement.import()")
));
$modal->EchoInterface();

$dbc->Close();
