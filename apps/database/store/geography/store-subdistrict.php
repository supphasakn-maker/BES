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
		"id"		=> "db_subdistricts.id",
		"name"		=> "db_subdistricts.name",
		"district"	=> "db_districts.name",
		"city"		=> "db_cities.name",
		"country"	=> "db_countries.name"
	);
	
	$table = array(
		"index" => "id",
		"name" => "db_subdistricts",
		"join" => array(
			array(
				"field" => "district",
				"table" => "db_districts",
				"with" => "id"
			),
			array(
				"field" => "city",
				"table" => "db_cities",
				"with" => "id"
			),
			array(
				"field" => "country",
				"table" => "db_countries",
				"with" => "id"
			)
		)
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








