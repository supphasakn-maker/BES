<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_stock_adjusted.id",
		"type" => "bs_stock_adjuest_types.name",
		"product_id" => "bs_stock_adjusted.product_id",
		"product" => "bs_products.name",
		"remark" => "bs_stock_adjusted.remark",
		"amount" => "bs_stock_adjusted.amount",
		"created" => "bs_stock_adjusted.created",
		"updated" => "bs_stock_adjusted.updated",
		"ref" => "bs_stock_adjusted.ref",
		"date" => "bs_stock_adjusted.date",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_adjusted",
		"join" => array(
			array(
				"field" => "product_id",
				"table" => "bs_products",
				"with" => "id"
			
			),array(
				"field" => "type_id",
				"table" => "bs_stock_adjuest_types",
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
