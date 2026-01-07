<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_stock_adjust_type_bwd.id",
		"type" => "bs_stock_adjust_type_bwd.type",
		"name" => "bs_stock_adjust_type_bwd.name"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_adjust_type_bwd",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
