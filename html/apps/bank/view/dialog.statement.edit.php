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
$statement = $dbc->GetRecord("bs_bank_statement", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_statement", "Edit Statement");
$modal->initiForm("form_editstatement");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.bank.statement.edit()")
));
$modal->SetVariable(array(
	array("id", $statement['id'])
));


$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Statement Name",
			"value" => $statement['date']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"caption" => "Type",
			"source" => array(
				array(1, "Debit"),
				array(2, "Credit")
			),
			"flex" => 3,
			"value" => $statement['type']
		),
		array(
			"name" => "amount",
			"placeholder" => "จำนวนเงิน",
			"flex" => 7,
			"value" => $statement['type'] == 1 ? $statement['amount'] : -$statement['amount']
		)
	),
	array(
		array(
			"name" => "balance",
			"caption" => "Balance",
			"placeholder" => "Balance",
			"value" => $statement['balance']
		)
	),
	array(
		array(
			"name" => "narrator",
			"caption" => "Narrator",
			"placeholder" => "รายละเอียด",
			"value" => $statement['narrator']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
