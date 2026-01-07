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
		"id" => "os_accounts.id",
		"name" => "os_accounts.name",
		"created" => "os_accounts.created",
		"updated" => "os_accounts.updated",
		"status" => "os_accounts.updated",
		"validated" => "os_accounts.updated",
		"org_id" => "os_accounts.org_id",
		"org_name" => "os_organizations.name",
		"data" => "os_accounts.data"
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_accounts",
		"join" => array(
			array(
				"field" => "org_id",
				"table" => "os_organizations",
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



