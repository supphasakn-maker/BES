<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', 1);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../../../include/iface.php";
include_once "../../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$bank = $dbc->GetRecord("bs_banks", "*", "id=" . $_POST['id']);
$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_edit_bank", "Edit Bank");
$modal->initiForm("form_editbank", "fn.app.database.company.bank.edit()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.company.bank.edit()")
));
$modal->SetVariable(array(
	array("id", $bank['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Bank Name",
			"value" => $bank['name']
		)
	),
	array(
		array(
			"name" => "number",
			"caption" => "Number",
			"value" => $bank['number']
		)
	),
	array(
		array(
			"name" => "branch",
			"caption" => "Branch",
			"value" => $bank['branch']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "detail",
			"caption" => "Detail",
			"placeholder" => "Detail",
			"value" => $bank['detail']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
