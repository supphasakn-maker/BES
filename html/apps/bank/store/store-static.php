<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_finance_static_values.id",
		"type" => "bs_finance_static_values.type",
		"start" => "bs_finance_static_values.start",
		"end" => "bs_finance_static_values.end",
		"title" => "bs_finance_static_values.title",
		"customer_name" => "bs_finance_static_values.customer_name",
		"amount" => "bs_finance_static_values.amount",
	);
	
	

	$table = array(
		"index" => "id",
		"name" => "bs_finance_static_values"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
