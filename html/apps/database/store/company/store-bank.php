<?php
	session_start();
	ini_set('display_errors',1);
	include_once "../../../../config/define.php";
	include_once "../../../../include/db.php";
	include_once "../../../../include/datastore.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new datastore;
	$dbc->Connect();
	
	$columns = array(
		"id"		=> "bs_banks.id",
		"name"		=> "bs_banks.name",
		"number"	=> "bs_banks.number",
		"branch"	=> "bs_banks.branch",
		"detail"	=> "bs_banks.detail",
		"created"	=> "bs_banks.created",
		"updated"	=> "bs_banks.updated",
		"icon"		=> "bs_banks.icon"
	);
	
	$table = array(
		"index" => "id",
		"name" => "bs_banks"
	);
	
	if(isset($_GET['where'])){
		$table["where"] = $_GET['where'];
	}
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>



