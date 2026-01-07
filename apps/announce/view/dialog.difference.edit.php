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
$rate_difference = $os->load_variable("rate_difference");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_edit_difference", "Change Difference");
$modal->initiForm("form_editdifference");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.announce.difference.edit()")
));

$blueprint = array(
	array(
		array(
			"name" => "rate_difference",
			"caption" => "DIFF",
			"placeholder" => "Rate Difference",
			"value" => $rate_difference
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
