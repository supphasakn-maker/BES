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
$modal->setModel("dialog_add_rollover", "Add Rollover");
$modal->initiForm("form_addrollover");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.sigmargin.rollover.add()")
));

$blueprint = array(
	array(
		array(
			"type" => "comboboxdb",
			"name" => "type",
			"caption" => "type",
			"source" => array(
				"table" => "bs_smg_rollover_type",
				"name" => "type",
				"value" => "type",
			),
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount",
			"caption" => "KGS",
			"placeholder" => "KGS	"
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "rate_spot",
			"caption" => "SPOT",
			"placeholder" => "SPOT"
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "trade",
			"caption" => "trade Date",
			"value" => date("Y-m-d"),
			"flex" => 4
		),
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Value Date",
			"value" => date("Y-m-d"),
			"flex" => 4
		)
	)

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
