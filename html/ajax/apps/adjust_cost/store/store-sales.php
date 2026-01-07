<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_sales_spot.id",
		"type" => "bs_sales_spot.type",
		"supplier_id" => "bs_sales_spot.supplier_id",
		"supplier" => "bs_suppliers.name",
		
		"rate_spot" => "FORMAT(bs_sales_spot.rate_spot,4)",
		"rate_pmdc" => "FORMAT(bs_sales_spot.rate_pmdc,4)",
		"net_spot" => "FORMAT(bs_sales_spot.rate_spot+bs_sales_spot.rate_pmdc,4)",
		"amount" => "FORMAT(bs_sales_spot.amount,4)",
		"spot_value" => "FORMAT(bs_sales_spot.amount*bs_sales_spot.rate_spot,4)",
		"spot_discount" => "FORMAT(bs_sales_spot.amount*bs_sales_spot.rate_pmdc,4)",
		"spot_net" => "FORMAT(bs_sales_spot.amount*(bs_sales_spot.rate_spot+bs_sales_spot.rate_pmdc),4)",
		
		"date" => "bs_sales_spot.date",
		"value_date" => "bs_sales_spot.value_date",
		"created" => "bs_sales_spot.created",
		"updated" => "bs_sales_spot.updated",
		"method" => "bs_sales_spot.method",
		"ref" => "bs_sales_spot.ref",
		"user" => "bs_sales_spot.user",
		"status" => "bs_sales_spot.status",
		"comment" => "bs_sales_spot.comment",
		"trade_id" => "bs_sales_spot.trade_id",
		"bank" => "bs_sales_spot.bank",
		"value" => "FORMAT(rate_spot*amount,4)",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_sales_spot",
		"join" => array(
			array(
				"field" => "supplier_id",
				"table" => "bs_suppliers",
				"with" => "id"
			)
		),
		"where" => "bs_sales_spot.adjust_id IS NULL"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
