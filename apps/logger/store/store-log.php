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
		"os_logs.id",
		"os_logs.datetime",
		"os_users.name",
		"os_logs.action",
		"os_logs.value",
		"os_logs.location",
		"os_logs.id"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_logs",
		"join" => array(
			array(
				"table" => "os_users",
				"field" => "user",
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