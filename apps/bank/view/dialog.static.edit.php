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
$static = $dbc->GetRecord("bs_finance_static_values", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_static", "Edit Static");
$modal->initiForm("form_editstatic");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.bank.static.edit()")
));
$modal->SetVariable(array(
	array("id", $static['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"caption" => "Type",
			"source" => array(
				array("1", "บวก"),
				array("2", "หัก")
			),
			"value" => $static['type']
		)
	),
	array(
		array(
			"name" => "title",
			"caption" => "Title",
			"placeholder" => "Static Title",
			"value" => $static['title']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "start",
			"caption" => "Start",
			"value" => date("Y-m-d"),
			"flex" => 4,
			"value" => $static['start']
		),
		array(
			"type" => "date",
			"name" => "end",
			"caption" => "End",
			"flex" => 4,
			"value" => $static['end']
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount",
			"value" => $static['amount']
		)
	),
	array(
		array(
			"name" => "customer_name",
			"caption" => "Customer",
			"value" => $static['customer_name']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
