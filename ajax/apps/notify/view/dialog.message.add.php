<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',1);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_message","Add Message");
	$modal->initiForm("form_addmessage");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.notify.message.add()")
	));
	$modal->SetVariable(array(
		array("org_id","")
	));

	$blueprint = array(
		array(
			array(
				"type" => "textarea",
				"name" => "message",
				"caption" => "Message",
				"placeholder" => "Message Name"
			)
		),array(
			array(
				"name" => "source",
				"caption" => "From",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "os_users",
					"value" => "id",
					"name" => "name"
				),
				"flex" => 4
			),
			array(
				"name" => "destination",
				"caption" => "To",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "os_users",
					"value" => "id",
					"name" => "name"
				),
				"flex" => 4
			),
		)
	);
	
	
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>