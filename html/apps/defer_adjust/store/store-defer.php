<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" 			=> "bs_adjust_defer.id",
	"supplier_id"	=> "bs_adjust_defer.supplier_id",
	"supplier" 		=> "bs_suppliers.name",
	"date" 		=> "bs_adjust_defer.date",
	"value_adjust_type" => "bs_adjust_defer.value_adjust_type",
	"product_id" => "bs_adjust_defer.product_id",
	"name" => "bs_products.name"
);

$table = array(
	"index" => "id",
	"name" => "bs_adjust_defer",
	"join" => array(
		array(
			"field" => "supplier_id",
			"table" => "bs_suppliers",
			"with" => "id"
		), array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		)
	)
);


if (isset($_GET['where'])) {
	$table['where'] = $_GET['where'];
}



$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
