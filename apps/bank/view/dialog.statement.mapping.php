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
		$statement = $dbc->GetRecord("bs_bank_statement", "*", "id=" . $this->param['id']);

		echo '<form name="form_mappingstatement" onsubmit="return false;">';
		echo '<input type="hidden" name="id" value="' . $statement['id'] . '">';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">Bank</label>';
		echo '<div class="col-sm-10">';
		echo '<select name="bank_id" class="form-control">';
		$sql = "SELECT * FROM bs_banks WHERE id != " . $statement['bank_id'];
		$rst = $dbc->Query($sql);
		while ($bank = $dbc->Fetch($rst)) {
			echo '<option value="' . $bank['id'] . '">' . $bank['name'] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">Date</label>';
		echo '<div class="col-sm-10">';
		echo '<input name="bank_date" type="date" class="form-control" value="' . $statement['date'] . '">';
		echo '</div>';
		echo '</div>';
		echo '<div class="form-group row">';
		echo '<label class="col-sm-2 col-form-label text-right">Line</label>';
		echo '<div class="col-sm-10">';
		echo '<select name="bank_line" class="form-control">';

		echo '</select>';
		echo '</div>';
		echo '</div>';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_mapping_statement", "Mapping Statement");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Mapping", "fn.app.bank.statement.mapping()")
));
$modal->EchoInterface();

$dbc->Close();
