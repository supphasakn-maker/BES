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

	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_add_bank","Add Bank");
	$modal->initiForm("form_addbank","fn.app.database.company.bank.add()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.database.company.bank.add()")
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Bank Name"
			)
		),array(
			array(
				"name" => "number",
				"caption" => "Number"
			)
		),array(
			array(
				"name" => "branch",
				"caption" => "Branch"
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "detail",
				"caption" => "Detail",
				"placeholder" => "Detail"
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>
