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

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_statement", "Add Statement");
$modal->initiForm("form_addstatement");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.bank.statement.add()")
));
$modal->SetVariable(array(
	array("bank_id", "")
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Statement Name",
			"value" => date("Y-m-d")
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
			"flex" => 3
		),
		array(
			"name" => "amount",
			"placeholder" => "จำนวนเงิน",
			"flex" => 7
		)
	),
	array(
		array(
			"name" => "balance",
			"caption" => "Balance",
			"placeholder" => "Balance"
		)
	),
	array(
		array(
			"name" => "narrator",
			"caption" => "Narrator",
			"placeholder" => "รายละเอียด"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
