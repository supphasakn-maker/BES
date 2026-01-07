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
	
	$contact = $dbc->GetRecord("os_contacts","*","id=".$_POST['id']);
	$address = $dbc->GetRecord("os_address","*","contact=".$contact['id']." AND priority=1");

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_edit_contact","Edit Contact");
	$modal->initiForm("form_editcontact");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.contact.contact.edit()")
	));
	
	$modal->SetVariable(array(
		array("contact_id",$contact['id']),
		array("address_id",$address['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"flex" => 5,
				"placeholder" => "Firstname",
				"value" => $contact['name']
			),array(
				"name" => "surname",
				"flex" => 5,
				"placeholder" => "Surname",
				"value" => $contact['surname']
			)
		),array(
			array(
				"type" => "combobox",
				"source" => array("นาย","นางสาว","Mr.","Miss."),
				"flex" => 2,
				"caption" => "title",
				"name" => "title",
				"value" => $contact['title']
			),array(
				"type" => "combobox",
				"source" => array(
					"male" => "Male",
					"female" => "Female"
				),
				"flex-label" => 1,
				"flex" => 2,
				"name" => "gender",
				"caption" => "Gender",
				"value" => $contact['gender']
			),array(
				"type" => "date",
				"name" => "dob",
				"flex-label" => 1,
				"flex" => 4,
				"placeholder" => "Date of Birth",
				"caption" => "DOB",
				"value" => $contact['dob']
			)
		),array(
			array(
				"name" => "citizen_id",
				"caption" => "Citizen ID",
				"flex" => 4,
				"placeholder" => "Citizen ID/Passport",
				"value" => $contact['citizen_id']
			),array(
				"name" => "nick",
				"caption" => "Nickname",
				"flex" => 4,
				"placeholder" => "Nickname",
				"value" => $contact['nickname']
			)
		),array(
			array(
				"name" => "skype",
				"caption" => "Skype",
				"flex" => 4,
				"placeholder" => "Skype ID",
				"value" => $contact['skype']
			),array(
				"name" => "facebook",
				"caption" => "Facebook",
				"flex" => 4,
				"placeholder" => "Facebook ID/Name",
				"value" => $contact['facebook']
			)
		),array(
			array(
				"name" => "google",
				"caption" => "Google",
				"flex" => 4,
				"placeholder" => "Google Plus",
				"value" => $contact['google']
			),array(
				"name" => "line",
				"caption" => "Line",
				"flex" => 4,
				"placeholder" => "Line",
				"value" => $contact['line']
			)
		),array(
			array(
				"name" => "phone",
				"caption" => "Phone",
				"value" => $contact['phone'],
				"flex" => 4,
				"placeholder" => "Phone Number"
			),array(
				"name" => "mobile",
				"caption" => "Mobile",
				"flex" => 4,
				"placeholder" => "Mobile Number",
				"value" => $contact['mobile']
			)
		),array(
			array(
				"name" => "email",
				"caption" => "E-Mail",
				"placeholder" => "E-Mail",
				"value" => $contact['email']
			)
		),array(
			array(
				"name" => "address",
				"caption" => "Address",
				"placeholder" => "Address",
				"value" => $address['address']
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
				"caption" => "Country",
				"value" => $address['country']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_cities",
					"value" => "id",
					"name" => "name",
					"where" => "country = ".$address['country']
				),
				"flex" => 4,
				"name" => "city",
				"caption" => "Province",
				"value" => $address['city']
			),array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_districts",
					"value" => "id",
					"name" => "name",
					"where" => "city = ".$address['city']
				),
				"flex" => 4,
				"name" => "district",
				"caption" => "District",
				"value" => $address['district']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "db_subdistricts",
					"value" => "id",
					"name" => "name",
					"where" => "district = ".$address['district']
				),
				"flex" => 4,
				"name" => "subdistrict",
				"caption" => "Subdistrict",
				"value" => $address['subdistrict']
			),array(
				"flex" => 4,
				"name" => "postal",
				"caption" => "Postal",
				"value" => $address['postal']
			)
		)
		
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
