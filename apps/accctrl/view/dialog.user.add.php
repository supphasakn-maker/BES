<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";
include_once "../../../include/session.php";



$dbc = new dbc;
$dbc->Connect();


$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_add_user", "Add User");
$modal->initiForm("form_adduser", "fn.app.accctrl.user.add()");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.accctrl.user.add()")
));



$blueprint = array(
	array(
		array(
			"name" => "username",
			"caption" => "Name",
			"placeholder" => "User Name",
			"value" => $_SESSION['auth']['user_id']
		)
	),
	array(
		array(
			"type" => "password",
			"name" => "password",
			"caption" => "Password",
			"placeholder" => "Your Password"
		)
	),
	array(
		array(
			"name" => "first",
			"caption" => "Name",
			"flex" => 5,
			"placeholder" => "Firstname"
		),
		array(
			"name" => "surname",
			"flex" => 5,
			"placeholder" => "Surname"
		)
	),
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_title",
			"flex" => 2,
			"name" => "title",
			"caption" => "Title"
		),
		array(
			"type" => "combobox",
			"source" => array(
				"male" => "Male",
				"female" => "Female"
			),
			"flex-label" => 1,
			"flex" => 2,
			"name" => "gender",
			"caption" => "Gender"
		),
		array(
			"name" => "dob",
			"flex-label" => 1,
			"flex" => 4,
			"placeholder" => "Date of Birth",
			"caption" => "DOB"
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "os_groups",
				"value" => "id",
				"name" => "name"
			),
			"flex" => 4,
			"name" => "gid",
			"caption" => "Group"
		),
		array(
			"name" => "nickname",
			"caption" => "Nickname",
			"flex" => 4,
			"placeholder" => "Nickname"
		)
	),
	array(
		array(
			"name" => "phone",
			"caption" => "Phone",
			"flex" => 4,
			"placeholder" => "Phone Number"
		),
		array(
			"name" => "mobile",
			"caption" => "Mobile",
			"flex" => 4,
			"placeholder" => "Mobile Number"
		)
	),
	array(
		array(
			"name" => "email",
			"caption" => "E-Mail",
			"placeholder" => "E-Mail"
		)
	),
	array(
		array(
			"name" => "address",
			"caption" => "Address",
			"placeholder" => "Address"
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "db_countries",
				"value" => "id",
				"name" => "name"
			),
			"name" => "country",
			"caption" => "Country"
		)
	),
	array(
		array(
			"type" => "combobox",
			"flex" => 4,
			"name" => "city",
			"caption" => "Province"
		),
		array(
			"type" => "combobox",
			"flex" => 4,
			"name" => "district",
			"caption" => "District"
		)
	),
	array(
		array(
			"type" => "combobox",
			"flex" => 4,
			"name" => "subdistrict",
			"caption" => "Subdistrict"
		),
		array(
			"flex" => 4,
			"name" => "postal",
			"caption" => "Postal"
		)
	)

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
