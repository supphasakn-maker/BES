<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', 1);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../../../include/iface.php";
include_once "../../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_add_currency", "Add Currency");
$modal->initiForm("form_addcurrency", "fn.app.database.company.currency.add()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.company.currency.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "code",
			"caption" => "Code",
			"placeholder" => "Currency Code"
		)
	),
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Currency Name"
		)
	),
	array(
		array(
			"name" => "value",
			"caption" => "Value",
			"placeholder" => "Currency Value"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
