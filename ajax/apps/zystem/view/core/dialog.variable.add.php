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

	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST); 
	$modal->setModel("dialog_add_variable","Add Variable");
	$modal->initiForm("form_addvariable");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.zystem.core.variable.add()")
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Variable Name"
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "value",
				"caption" => "Vakue",
				"placeholder" => "Value"
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>