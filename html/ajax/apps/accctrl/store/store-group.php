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
		"id" => "os_groups.id",
		'name' => "os_groups.name",
		'created' => "os_groups.created",
		'account' => "os_accounts.name",
		'updated' => "os_groups.updated",
		
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_groups",
		"join" => array(
			array(
				"field" => "account",
				"table" => "os_accounts",
				"with" => "id"
			)
		)
	);
	
	if(isset($_GET['account']) && $_GET['account']!=""){
		$table["where"] = "os_groups.account=".$_GET['account'];
	}
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>
