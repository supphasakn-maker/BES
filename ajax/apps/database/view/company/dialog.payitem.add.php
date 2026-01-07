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
	$modal->setModel("dialog_add_payitem","Add payitem");
	$modal->initiForm("form_addpayitem","fn.app.database.company.payitem.add()");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.database.company.payitem.add()")
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Payitem Name"
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
				)
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
	
?>
