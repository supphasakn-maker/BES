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
	
	$notification = $dbc->GetRecord("os_notifications","*","id=".$_POST['id']);
	
	$os = new oceanos($dbc);
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_edit_notification","Edit Notification");
	$modal->initiForm("form_editnotification");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.notify.notification.edit()")
	));
	
	$modal->SetVariable(array(
		array("txtID",$notification['id'])
	));

	$blueprint = array(
		array(
			array(
				"name" => "type",
				"caption" => "Type",
				"type" => "combobox",
				"source" => array(
					array("notify","Notify"),
					array("alert","Alert"),
					array("schedule","Schedule")
				),
				"flex" => 4,
				"value" => $notification['type']
			),
			array(
				"name" => "user",
				"caption" => "To",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "os_users",
					"value" => "id",
					"name" => "name"
				),
				"flex" => 4,
				"value" => $notification['user']
			),
		),array(
			array(
				"name" => "topic",
				"caption" => "Message",
				"placeholder" => "Message Name",
				"value" => $notification['topic']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "detail",
				"caption" => "Detail",
				"rows" => 5,
				"placeholder" => "Message Name",
				"value" => $notification['detail']
			)
		),
	);
	
	
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>