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
$modal->setModel("dialog_add_city", "Add City");
$modal->initiForm("form_addcity", "fn.app.database.city.add()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.city.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "City Name"
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "db_countries",
				"value" => "id",
				"name" => "name"
			),
			"name" => "country",
			"caption" => "Country"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
