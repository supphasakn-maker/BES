<?php
	session_start();
	include_once "../../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	include_once "../../../../include/db.php";
	include_once "../../../../include/oceanos.php";
	include_once "../../../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);
	$country = $dbc->GetRecord("db_countries","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_country","Edit Country");
	$modal->initiForm("form_editcountry","fn.app.database.country.edit()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-default","Save Change","fn.app.database.country.edit()")
	));
	$modal->SetVariable(array(
		array("txtID",$country['id'])
	));
	
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Country Name",
				"value" => $country['name']
			)
		),
		array(
			array(
				"name" => "txtLocal",
				"caption" => "Local Name",
				"placeholder" => "Local Country Name",
				"value" => $country['local_name']
			)
		),
		array(
			array(
				"name" => "txtISO",
				"caption" => "ISO",
				"flex" => 4,
				"placeholder" => "Abbreviation 2 Code",
				"value" => $country['iso']
			),
			array(
				"name" => "txtISO3",
				"caption" => "ISO3",
				"flex" => 4,
				"placeholder" => "Abbreviation 3 Code",
				"value" => $country['iso3']
			)
		),
		array(
			array(
				"name" => "txtPhone",
				"caption" => "PhoneCode",
				"placeholder" => "Country Phone Code",
				"value" => $country['phonecode']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>