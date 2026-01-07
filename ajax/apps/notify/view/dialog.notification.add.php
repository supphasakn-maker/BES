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
	$modal->setModel("dialog_add_notification","Add Notification");
	$modal->initiForm("form_addnotification");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.notify.notification.add()")
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
				"flex" => 4
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
				"flex" => 4
			),
		),array(
			array(
				"name" => "topic",
				"caption" => "Message",
				"placeholder" => "Message Name"
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "detail",
				"caption" => "Detail",
				"rows" => 5,
				"placeholder" => "Message Name"
			)
		),
	);
	
	
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>