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
$rate_recycle2 = $os->load_variable("rate_recycle2");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_rate_recycle2", "Change Recycle 2");
$modal->initiForm("form_change_rate_recycle2");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.datapanel.master.change_rate_recycle2()")
));

$blueprint = array(
	array(
		array(
			"name" => "rate",
			"caption" => "Rate",
			"placeholder" => "Exchange Rate",
			"value" => $rate_recycle2
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
