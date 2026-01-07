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
$change_buy = $os->load_variable("change_buy");


$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_change_buy", "Change Buy");
$modal->initiForm("form_change_buy");

$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Change", "fn.app.announce.difference.change_buy()")
));

$blueprint = array(
	array(
		array(
			"name" => "change_buy",
			"caption" => "Buy",
			"placeholder" => "Change Buy",
			"value" => $change_buy
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();

$dbc->Close();
