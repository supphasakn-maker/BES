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
$subdistrict = $dbc->GetRecord("db_subdistricts", "*", "id=" . $_REQUEST['id']);
$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_edit_subdistrict", "Edit Subdistrict");
$modal->initiForm("form_editsubdistrict", "fn.app.database.subdistrict.edit()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.database.subdistrict.edit()")
));
$modal->SetVariable(array(
	array("txtID", $subdistrict['id'])
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
			"value" => $subdistrict['country']
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
			"value" => $subdistrict['city']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "db_districts",
				"value" => "id",
				"name" => "name"
			),
			"name" => "subdistrict",
			"caption" => "Subdistrict",
			"value" => $subdistrict['district']
		)
	),
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Subdistrict Name",
			"value" => $subdistrict['name']
		)
	)
);


$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
