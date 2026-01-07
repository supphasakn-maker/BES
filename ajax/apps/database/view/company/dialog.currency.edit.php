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
	$currency = $dbc->GetRecord("bs_currencies","*","id=".$_POST['id']);
	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_currency","Edit Currency");
	$modal->initiForm("form_editcurrency","fn.app.database.company.currency.edit()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.database.company.currency.edit()")
	));
	$modal->SetVariable(array(
		array("id",$currency['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "code",
				"caption" => "Code",
				"placeholder" => "Currency Code",
				"value" => $currency['code']
			)
		),array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Currency Name",
				"value" => $currency['name']
			)
		),array(
			array(
				"name" => "value",
				"caption" => "Value",
				"placeholder" => "Currency Value",
				"value" => $currency['value']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>