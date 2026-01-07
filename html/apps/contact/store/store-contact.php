<?php
	session_start();
	ini_set('display_errors',1);
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new datastore;
	$dbc->Connect();
	
	$columns = array(
		"id" => "os_contacts.id",
		"fullname" => "CONCAT(os_contacts.name,' ',os_contacts.surname)",
		"title" => "os_contacts.title",
		"name" => "os_contacts.name",
		"surname" => "os_contacts.surname",
		"dob" => "os_contacts.dob",
		"gender" => "os_contacts.gender",
		"email" => "os_contacts.email",
		"phone" => "os_contacts.phone",
		"mobile" => "os_contacts.mobile",
		"created" => "os_contacts.created",
		"updated" => "os_contacts.updated",
		"status" => "os_contacts.status",
		"skype" => "os_contacts.skype",
		"facebook" => "os_contacts.facebook",
		"google" => "os_contacts.google",
		"citizen_id" => "os_contacts.citizen_id",
		"avatar" => "os_contacts.avatar",
		"nickname" => "os_contacts.nickname",
		"data" => "os_contacts.data",
		"fulladdress" => "os_address.fulladdress"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_contacts",
		"join" => array(
			array(
				"field" => "id",
				"table" => "os_address",
				"with" => "contact"
			)
		),
		"where" => "os_address.priority = 1"
	);
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>









