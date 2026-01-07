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
		"id" => "bs_orders.id",
		"code" => "bs_orders.code",
		"customer_id" => "bs_orders.customer_id",
		"customer_name" => "bs_orders.customer_name",
		"date" => "bs_orders.date",
		"sales" => "bs_employees.fullname",
		"amount" => "bs_orders.amount",
		"price" => "bs_orders.price",
		"delivery_date" => "bs_orders.delivery_date",
		"total" => "bs_orders.total",
		"net" => "bs_orders.net",
		"status" => "bs_orders.status",
		"created" => "bs_orders.created",
		"updated" => "bs_orders.updated"
	);
	
	$table = array(
		"index" => "id",
		"name" => "bs_orders",
		"join" => array(
			array(
				"field" => "sales",
				"table" => "bs_employees",
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



