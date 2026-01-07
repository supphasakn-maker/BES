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
		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $this->param['id']);
		echo '<form name="form_splitusd">';
		echo '<input type="hidden" name="id" value="' . $usd['id'] . '">';
		echo '<table class="table table-bordered table-form">';
		echo '<tbody>';
		echo '<tr>';
		echo '<td><label>จำนวนก่อนแบ่ง</label></td>';
		echo '<td><input name="amount" type="text" class="form-control" readonly value="' . $usd['amount'] . '"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>ใบที่หนึ่ง</label></td>';
		echo '<td><input name="split" type="text" class="form-control" value="0.00"></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td><label>ใบที่สอง</label></td>';
		echo '<td><input name="remain" type="text" class="form-control" value="0.00"></td>';
		echo '</tr>';
		echo '</tbody>';
		echo '</table>';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_split_usd", "Split Usd");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-danger", "Split", "fn.app.purchase.usd.split()")
));
$modal->EchoInterface();

$dbc->Close();
