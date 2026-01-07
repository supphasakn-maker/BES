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
		"id" => "os_messages.id",
		"fullname" => "CONCAT(os_contacts.name,' ',os_contacts.surname)",
		"phone" => "os_contacts.phone",
		"mobile" => "os_contacts.mobile",
		"msg" => "os_messages.msg",
		"created" => "os_messages.created",
		"updated" => "os_messages.updated",
		"acknowledge" => "os_messages.acknowledge",
		"opened" => "os_messages.opened",
		"type" => "os_messages.type"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_messages",
		"join" => array(
			array(
				"field" => "source",
				"table" => "os_users",
				"with" => "id"
			),
			array(
				"join" => "os_users",
				"field" => "contact",
				"table" => "os_contacts",
				"with" => "id"
			)
		)
	);
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>



