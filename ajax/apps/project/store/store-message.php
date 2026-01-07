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
		"id" => "messages.id",
		"fullname" => "CONCAT(contacts.name,' ',contacts.surname)",
		"phone" => "contacts.phone",
		"mobile" => "contacts.mobile",
		"msg" => "messages.msg",
		"created" => "messages.created",
		"updated" => "messages.updated",
		"acknowledge" => "messages.acknowledge",
		"opened" => "messages.opened",
		"type" => "messages.type"
	);
	
	$table = array(
		"index" => "id",
		"name" => "messages",
		"join" => array(
			array(
				"field" => "source",
				"table" => "users",
				"with" => "id"
			),
			array(
				"join" => "users",
				"field" => "contact",
				"table" => "contacts",
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



