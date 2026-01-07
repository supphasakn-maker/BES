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
$scb_rate = $os->load_variable("scb_rate");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_scb_master", "Change SCB Rate");
$modal->initiForm("form_change_scbmaster");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.rate_exchange.master.change_scb()")
));

$blueprint = array(
	array(
		array(
			"name" => "rate",
			"caption" => "RATE",
			"placeholder" => "SCB RATE",
			"value" => $scb_rate
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
