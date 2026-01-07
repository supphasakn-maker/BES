<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$os = new oceanos($dbc);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_contact","Add Contact");
	$modal->initiForm("form_addcontact");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.contact.add()")
	));
	
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"flex" => 5,
				"placeholder" => "Firstname"
			),array(
				"name" => "surname",
				"flex" => 5,
				"placeholder" => "Surname"
			)
		),array(
			array(
				"type" => "combobox",
				"source" => array("นาย","นางสาว","Mr.","Miss."),
				"flex" => 2,
				"name" => "title",
				"caption" => "Title"
			),array(
				"type" => "combobox",
				"source" => array(
					"male" => "Male",
					"female" => "Female"
				),
				"flex-label" => 1,
				"flex" => 2,
				"name" => "gender",
				"caption" => "Gender"
			),array(
				"type" => "date",
				"name" => "dob",
				"flex-label" => 1,
				"flex" => 4,
				"placeholder" => "Date of Birth",
				"caption" => "DOB"
			)
		),array(
			array(
				"name" => "citizen_id",
				"caption" => "Citizen ID",
				"flex" => 4,
				"placeholder" => "Citizen ID/Passport"
			),array(
				"name" => "nickname",
				"caption" => "Nickname",
				"flex" => 4,
				"placeholder" => "Nickname"
			)
		),array(
			array(
				"name" => "skype",
				"caption" => "Skype",
				"flex" => 4,
				"placeholder" => "Skype ID"
			),array(
				"name" => "facebook",
				"caption" => "Facebook",
				"flex" => 4,
				"placeholder" => "Facebook ID/Name"
			)
		),array(
			array(
				"name" => "google",
				"caption" => "Google",
				"flex" => 4,
				"placeholder" => "Google Plus"
			),array(
				"name" => "line",
				"caption" => "Line",
				"flex" => 4,
				"placeholder" => "Line"
			)
		),array(
			array(
				"name" => "phone",
				"caption" => "Phone",
				"flex" => 4,
				"placeholder" => "Phone Number"
			),array(
				"name" => "mobile",
				"caption" => "Mobile",
				"flex" => 4,
				"placeholder" => "Mobile Number"
			)
		),array(
			array(
				"name" => "email",
				"caption" => "E-Mail",
				"placeholder" => "E-Mail"
			)
		),array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address"
			)
		),array(
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
		),array(
			array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "city",
				"caption" => "Province"
			),array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "district",
				"caption" => "District"
			)
		),array(
			array(
				"type" => "combobox",
				"flex" => 4,
				"name" => "subdistrict",
				"caption" => "Subdistrict"
			),array(
				"flex" => 4,
				"name" => "postal",
				"caption" => "Postal"
			)
		)
		
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
