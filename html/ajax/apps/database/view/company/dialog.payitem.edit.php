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
	$payitem = $dbc->GetRecord("bs_payment_types","*","id=".$_POST['id']);
	$modal = new imodal($dbc,$os->auth);
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_payitem","Edit payitem");
	$modal->initiForm("form_editpayitem","fn.app.database.company.payitem.edit()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.database.company.payitem.edit()")
	));
	$modal->SetVariable(array(
		array("id",$payitem['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Payitem Name",
				"value" => $payitem['name']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "negative",
				"caption" => "Negative",
				"source" => array(
					array("1","Yes"),
					array("0","No"),
					array("2","Order")
				),
				"value" => $payitem['negative']
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>