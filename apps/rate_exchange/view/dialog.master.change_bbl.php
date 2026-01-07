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
$bbl_rate = $os->load_variable("bbl_rate");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_bbl_master", "Change BBL Rate");
$modal->initiForm("form_change_bblmaster");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.rate_exchange.master.change_bbl()")
));

$blueprint = array(
	array(
		array(
			"name" => "rate",
			"caption" => "RATE",
			"placeholder" => "BBL RATE",
			"value" => $bbl_rate
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
