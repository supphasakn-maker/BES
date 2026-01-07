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
$modal->setModel("dialog_add_ohter", "Add Ohter");
$modal->initiForm("form_addohter");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.sigmargin.ohter.add()")
));

$blueprint = array(
	array(
		array(
			"type" => "number",
			"name" => "usd_debit",
			"caption" => "Dabit (USD)",
			"placeholder" => "Dabit (USD)"
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "usd_credit",
			"caption" => "Credit (USD)",
			"placeholder" => "Credit (USD)"
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount_debit",
			"caption" => "Debit (Silver)",
			"placeholder" => "Debit (Silver)"
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount_credit",
			"caption" => "Credit (Silver)",
			"placeholder" => "Credit (Silver)"
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d"),
			"flex" => 4
		)
	),
	array(
		array(
			"type" => "input",
			"name" => "remark",
			"caption" => "Remark",
			"placeholder" => "Remark"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
