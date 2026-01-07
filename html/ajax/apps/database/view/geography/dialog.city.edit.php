<?php
	session_start();
	include_once "../../../../config/define.php";
	@ini_set('display_errors',1);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);
	$city = $dbc->GetRecord("db_cities","*","id=".$_REQUEST['id']);
	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_city","Edit City");
	$modal->initiForm("form_editcity","fn.app.database.city.edit()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.database.city.edit()")
	));
	$modal->SetVariable(array(
		array("txtID",$city['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "City Name",
				"value" => $city['name']
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
				"caption" => "Country",
				"value" => $city['country']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>