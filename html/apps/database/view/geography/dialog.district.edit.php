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
$district = $dbc->GetRecord("db_districts", "*", "id=" . $_POST['id']);
$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_edit_district", "Edit District");
$modal->initiForm("form_editdistrict", "fn.app.database.district.edit()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.district.edit()")
));
$modal->SetVariable(array(
	array("txtID", $district['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "db_countries",
				"value" => "id",
				"name" => "name"
			),
			"name" => "country",
			"caption" => "Country",
			"value" => $district['country']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "db_cities",
				"value" => "id",
				"name" => "name"
			),
			"name" => "city",
			"caption" => "City",
			"value" => $district['city']
		)
	),
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "District Name",
			"value" => $district['name']
		)
	)
);


$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
