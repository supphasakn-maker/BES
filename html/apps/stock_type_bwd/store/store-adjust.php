<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_stock_adjusted_bwd.id",
        "product_id" => "bs_stock_adjusted_bwd.product_id",
        "name_product" => "bs_products_bwd.name",
        "remark" => "bs_stock_adjusted_bwd.remark",
        "weight_expected" => "bs_stock_adjusted_bwd.weight_expected",
		"amount" => "bs_stock_adjusted_bwd.amount",
        "created" => "bs_stock_adjusted_bwd.created",
        "updated" => "bs_stock_adjusted_bwd.updated",
        "date" => "bs_stock_adjusted_bwd.date",
        "name" => "bs_products_type.name",
        "type_id" => "bs_stock_adjusted_bwd.type_id",
        "product_type" => "bs_stock_adjusted_bwd.product_type",
        "name_type" => "bs_stock_adjust_type_bwd.name",

	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_adjusted_bwd",
		"join" => array(
			array(
				"field" => "product_id",
				"table" => "bs_products_bwd",
				"with" => "id"
			
			),array(
				"field" => "product_type",
				"table" => "bs_products_type",
				"with" => "id"
			
			),array(
				"field" => "type_id",
				"table" => "bs_stock_adjust_type_bwd",
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
