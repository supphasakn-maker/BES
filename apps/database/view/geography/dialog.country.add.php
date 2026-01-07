<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
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
$modal->setModel("dialog_add_country", "Add Country");
$modal->initiForm("form_addcountry", "fn.app.database.country.add()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.country.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Country Name"
		)
	),
	array(
		array(
			"name" => "txtLocal",
			"caption" => "Local Name",
			"placeholder" => "Local Country Name"
		)
	),
	array(
		array(
			"name" => "txtISO",
			"caption" => "ISO",
			"flex" => 4,
			"placeholder" => "Abbreviation 2 Code"
		),
		array(
			"name" => "txtISO3",
			"caption" => "ISO3",
			"flex" => 4,
			"placeholder" => "Abbreviation 3 Code"
		)
	),
	array(
		array(
			"name" => "txtPhone",
			"caption" => "PhoneCode",
			"placeholder" => "Country Phone Code"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
