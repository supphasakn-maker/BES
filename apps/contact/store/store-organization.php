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
		"id" => 'os_organizations.id',
		"code" => "os_organizations.code",
		"name" => "os_organizations.name",
		"email" => 'os_organizations.email',
		"phone" => 'os_organizations.phone',
		"fax" => 'os_organizations.fax',
		"website" => 'os_organizations.website',
		"type" => "os_organizations.type",
		"created" => "os_organizations.created",
		"updated" => "os_organizations.updated",
		"tax_id" => "os_organizations.tax_id",
		"branch" => "os_organizations.branch",
		"logo" => "os_organizations.logo",
		"os_address_id" => 'os_address.id',
		"account" => 'os_organizations.id',
		"os_address" => 'os_address.address',
		"country" => 'os_address.id',
		"city" => 'os_address.city',
		"district" => 'os_address.district',
		"subdistrict" => 'os_address.subdistrict',
		"postal" => 'os_address.postal',
	);
	
	$table = array(
		"index" => "id",
		"name" => "os_organizations",
		"join" => array(
			array(
				"field" => "id",
				"table" => "os_address",
				"with" => "organization AND os_address.priority=1"
			)
		)
	);
	
	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());
	
	$dbc->Close();

?>









