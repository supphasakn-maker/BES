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
		"source" => "os_users.name",
		"destination" => "os_messages.destination",
		"type" => "os_messages.type",
		"message" => "os_messages.msg",
		"created" => "os_messages.created",
		"updated" => "os_messages.updated",
		"opened" => "os_messages.opened",
		"acknowledge" => "os_messages.acknowledge"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_messages",
		"join" => array(
			array(
				"table" => "os_users",
				"field" => "source",
				"with" => "id",
			)
		)
	);
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>