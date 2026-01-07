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
		"id" => "contacts.id",
		"fullname" => "CONCAT(contacts.name,' ',contacts.surname)",
		"title" => "contacts.title",
		"name" => "contacts.name",
		"surname" => "contacts.surname",
		"dob" => "contacts.dob",
		"gender" => "contacts.gender",
		"email" => "contacts.email",
		"phone" => "contacts.phone",
		"mobile" => "contacts.mobile",
		"joined" => "contacts.joined",
		"created" => "contacts.created",
		"updated" => "contacts.updated",
		"status" => "contacts.status",
		"website" => "contacts.website",
		"skype" => "contacts.skype",
		"facebook" => "contacts.facebook",
		"google" => "contacts.google",
		"citizen_id" => "contacts.citizen_id",
		"avatar" => "contacts.avatar",
		"nickname" => "contacts.nickname",
		"line" => "contacts.line",
		"customer_id" => "customers.id",
		"user_id" => "users.id",
		"username" => "users.name",
		"supplier_id" => "suppliers.id",
		"employee_id" => "employees.id",
		"supplier" => "suppliers.code",
		"customer" => "customers.code"
	);
	
	$table = array(
		"index" => "id",
		"name" => "contacts",
		"join" => array(
			array(
				"field" => "id",
				"table" => "customers",
				"with" => "contact"
			),
			array(
				"field" => "id",
				"table" => "users",
				"with" => "contact"
			),
			array(
				"field" => "id",
				"table" => "suppliers",
				"with" => "contact"
			),
			array(
				"field" => "id",
				"table" => "employees",
				"with" => "contact"
			)
		)
	);
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>









