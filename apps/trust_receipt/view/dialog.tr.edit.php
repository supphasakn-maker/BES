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
$tr = $dbc->GetRecord("bs_transfers", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_tr", "Edit Tr");
$modal->initiForm("form_edittr");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.trust_receipt.tr.edit()")
));
$modal->SetVariable(array(
	array("id", $tr['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "code",
			"caption" => "เลขที่ตั๋วสัญญา",
			"placeholder" => "Code",
			"value" => $tr['code']
		)
	),
	array(
		array(
			"name" => "remark",
			"caption" => "Remark",
			"placeholder" => "Remark",
			"value" => $tr['remark']
		)
	),
	array(
		array(
			"name" => "rate_interest",
			"caption" => "Rate Interest",
			"placeholder" => "Rate Interest",
			"value" => $tr['rate_interest']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "due_date",
			"caption" => "Due Date",
			"placeholder" => "Due Date",
			"value" => $tr['due_date']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
