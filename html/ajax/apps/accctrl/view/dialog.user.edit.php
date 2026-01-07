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
	//$modal->setParam($_POST);
	$modal->setModel("dialog_edit_user","Edit User");
	$modal->initiForm("form_edituser");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.accctrl.user.edit()")
	));
	
	$user = $dbc->GetRecord("os_users","*","id=".$_POST['id']);
	$contact = $dbc->GetRecord("os_contacts","*","id=".$user['contact']);
	$address = $dbc->GetRecord("os_address","*","contact=".$contact['id']);
	
	
	$blueprint = array(
		array(
			array(
				"name" => "username",
				"caption" => "Name",
				"placeholder" => "User Name",
				"value" => $user['name']
			)
		),array(
			array(
				"type" => "password",
				"name" => "password",
				"caption" => "Password",
				"placeholder" => "Leave blank is No Change"
			)
		),array(
			array(
				"name" => "first",
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
				"type" => "comboboxdatabank",
				"source" => "db_title",
				"flex" => 2,
				"name" => "title",
				"caption" => "Title",
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
				"name" => "dob",
				"flex-label" => 1,
				"flex" => 4,
				"placeholder" => "Date of Birth",
				"caption" => "DOB",
				"value" => $contact['dob']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"source" => array( 
					"table" => "os_groups",
					"value" => "id",
					"name" => "name"
				),
				"flex" => 4,
				"name" => "gid",
				"caption" => "Group",
				"value" => $user['gid']
			),array(
				"name" => "nickname",
				"caption" => "Nickname",
				"flex" => 4,
				"placeholder" => "Nickname",
				"value" => $contact['nickname']
			)
		),array(
			array(
				"name" => "phone",
				"caption" => "Phone",
				"flex" => 4,
				"placeholder" => "Phone Number",
				"value" => $contact['phone']
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
	$modal->SetVariable(array(
		array("user_id",$user['id']),
		array("contact_id",$contact['id']),
		array("address_id",$address['id'])
	));
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>