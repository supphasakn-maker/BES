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
$modal->setModel("dialog_add_static", "Add Static");
$modal->initiForm("form_addstatic");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.bank.static.add()")
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
			)
		)
	),
	array(
		array(
			"name" => "title",
			"caption" => "Title",
			"placeholder" => "Static Title"
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "start",
			"caption" => "Start",
			"value" => date("Y-m-d"),
			"flex" => 4
		),
		array(
			"type" => "date",
			"name" => "end",
			"caption" => "End",
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount"
		)
	),
	array(
		array(
			"name" => "customer_name",
			"caption" => "Customer"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
