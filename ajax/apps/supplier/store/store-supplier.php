<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_suppliers.id",
		"name" => "bs_suppliers.name",
		"created" => "bs_suppliers.created",
		"updated" => "bs_suppliers.updated",
		"comment" => "bs_suppliers.comment",
		"type" => "bs_suppliers.type",
		"gid" => "bs_suppliers.gid",
		"group_name" => "bs_supplier_groups.name"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_suppliers",
		"join" => array(
			array(
				"field" => "gid",
				"table" => "bs_supplier_groups",
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
