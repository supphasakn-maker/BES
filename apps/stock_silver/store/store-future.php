<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => " bs_stock_silver.id",
		"code" => "bs_stock_silver.code",
		"customer_po" => "bs_orders_buy.code",
		"customer" => "bs_stock_silver.customer_po",
        "pack_name" => "bs_stock_silver.pack_name",
        "pack_type" => "bs_stock_silver.pack_type",
        "weight_actual" => "bs_stock_silver.weight_actual",
        "created" => "bs_stock_silver.created",
        "status" => "bs_stock_silver.status",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_silver",
		"join" => array(
			array(
				"field" => "customer_po",
				"table" => "bs_orders_buy",
				"with" => "id"
			)
		),
		"where" => "bs_stock_silver.stock = 'BWF' AND bs_stock_silver.status = 0"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
