<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_employee", "Edit Employee");
$modal->initiForm("form_editemployee");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-default", "Save Change", "fn.app.employee.employee.edit()")
));
$modal->SetVariable(array(
	array("id", $employee['id'])
));


$blueprint = array(
	array(
		array(
			"name" => "fullname",
			"caption" => "Full Name",
			"placeholder" => "Employee Name",
			"value" => $employee['fullname']
		)
	),
	array(
		array(
			"name" => "nickname",
			"caption" => "Nickname",
			"placeholder" => "Nickname",
			"flex" => 4,
			"value" => $employee['nickname']
		),
		array(
			"type" => "date",
			"name" => "dob",
			"caption" => "DOB",
			"placeholder" => "",
			"flex" => 4,
			"value" => $employee['dob']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"name" => "department",
			"caption" => "Department",
			"source" => array(
				"table" => "bs_departments",
				"name" => "name",
				"value" => "id"
			),
			"flex" => 3,
			"value" => $employee['department']
		),
		array(
			"type" => "comboboxdb",
			"name" => "user",
			"caption" => "User",
			"source" => array(
				"table" => "os_users",
				"name" => "name",
				"value" => "id"
			),
			"default" => array(
				"value" => "NULL",
				"name" => "Not Selected"
			),
			"flex" => 5,
			"value" => $employee['user']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
