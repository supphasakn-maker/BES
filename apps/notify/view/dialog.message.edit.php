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
	
	$message = $dbc->GetRecord("os_messages","*","id=".$_POST['id']);
	
	$os = new oceanos($dbc);
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_edit_message","Add Message");
	$modal->initiForm("form_editmessage");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.notify.message.edit()")
	));
	$modal->SetVariable(array(
		array("id",$message['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "textarea",
				"name" => "message",
				"caption" => "Message",
				"placeholder" => "Message Name",
				"value" => $message['msg']
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
				"flex" => 4,
				"value" => $message['source']
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
				"flex" => 4,
				"value" => $message['destination']
			),
		)
	);
	
	
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>