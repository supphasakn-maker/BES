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
		$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $this->param['id']);
		if (is_null($customer['imgs'])) {
			$imgs = array();
		} else {
			$imgs = json_decode($customer['imgs'], true);
		}
		echo '<table id="tblDoucment" class="table">';
		echo '<tbody>';
		for ($i = 0; $i < count($imgs); $i++) {
			echo '<tr data-file="' . $imgs[$i] . '">';
			echo '<td><a href="' . $imgs[$i] . '">' . $imgs[$i] . '</a></td>';
			echo '<td><a onclick="fn.app.customer.customer.document_remove(' . $this->param['id'] . ',' . $i . ')" class="btn btn-sm btn-danger">Remove</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '<form style="display:none;" name="uploader" >';
		echo '<input type="hidden" name="id" value="' . $this->param['id'] . '">';
		echo '<input type="file" name="file[]" multiple>';
		echo '</form>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_document_customer", "Dialog Documents");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Upload", "fn.app.customer.customer.document_upload(" . $_POST['id'] . ")"),
	array("action", "btn-warning", "Sort", "fn.app.customer.customer.document_sort(" . $_POST['id'] . ")")
));
$modal->EchoInterface();

$dbc->Close();
