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
	$variable = $dbc->GetRecord("os_variable","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_variable","Edit Variable");
	$modal->initiForm("form_editvariable");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.zystem.core.variable.edit()")
	));
	$modal->SetVariable(array(
		array("id",$variable['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Variable Name",
				"value" => $variable['name']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "value",
				"caption" => "Vakue",
				"placeholder" => "Value",
				"value" => $variable['value']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>