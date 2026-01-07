<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => " bs_stock_bwd.id",
		"code" => "bs_stock_bwd.code",
        "pack_name" => "bs_stock_bwd.pack_name",
        "pack_type" => "bs_stock_bwd.pack_type",
        "weight_expected" => "bs_stock_bwd.weight_expected",
        "amount" => "bs_stock_bwd.amount",
        "status" => "bs_stock_bwd.status",
        "product_type" => "bs_stock_bwd.product_type",
        "submited" => "bs_stock_bwd.submited",
        "product_id" => "bs_stock_bwd.product_id",
		"name_product" => "bs_products_bwd.name",
		"name" => "bs_products_type.name",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_bwd",
		"join" => array(
			array(
				"field" => "product_id",
				"table" => "bs_products_bwd",
				"with" => "id"
			),array(
				"field" => "product_type",
				"table" => "bs_products_type",
				"with" => "id"
			)
		),
		"where" => "bs_stock_bwd.status = 0"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
