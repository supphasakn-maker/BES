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
$pmdc_rate = $os->load_variable("pmdc_rate");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_pmdc_change", "Change Premium/Discount Rate");
$modal->initiForm("form_pmdc_change");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.announce.difference.pmdc_change()")
));

$blueprint = array(
	array(
		array(
			"name" => "pmdc_rate",
			"caption" => "Rate",
			"placeholder" => "Premium/Discount Rate",
			"value" => $pmdc_rate
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
