<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$tr = $dbc->GetRecord("bs_transfer_usd", "*", "purchase_id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_edit_tr", "Edit Tr");
$modal->initiForm("form_edittr");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.trust_receipt.tr.change_date()")
));
$modal->SetVariable(array(
	array("id", $tr['purchase_id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Date",
			"value" => $tr['date']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
