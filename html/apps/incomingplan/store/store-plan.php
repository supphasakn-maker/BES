<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_incoming_plans.id",
	"import_id" => "bs_incoming_plans.import_id",
	"created" => "bs_incoming_plans.created",
	"updated" => "bs_incoming_plans.updated",
	"user_id" => "bs_incoming_plans.user_id",
	"import_date" => "bs_incoming_plans.import_date",
	"import_brand" => "bs_incoming_plans.import_brand",
	"brand" => "bs_incoming_plans.brand",
	"import_lot" => "bs_incoming_plans.import_lot",
	"amount" => "FORMAT(bs_incoming_plans.amount,4)",
	"rate_pmdc" => "bs_incoming_plans.rate_pmdc",
	"factory" => "bs_incoming_plans.factory",
	"product_type_id" => "bs_incoming_plans.product_type_id",
	"product" => "bs_products.name",
	"user" => "os_users.name",
	"coa" => "bs_incoming_plans.coa",
	"country" => "bs_incoming_plans.country",
	"coc" => "bs_incoming_plans.coc",
	"remark" => "bs_products_import.name",
	"parent" => "bs_incoming_plans.parent",
	"bank_date" => "bs_incoming_plans.bank_date",
	"supplier_id" => "bs_incoming_plans.supplier_id",
	"supplier_name" => "bs_suppliers.name",
	"usd" => "FORMAT(bs_incoming_plans.usd,4)",

);

$table = array(
	"index" => "id",
	"name" => "bs_incoming_plans",
	"join" => array(
		array(
			"field" => "user_id",
			"table" => "os_users",
			"with" => "id"
		),
		array(
			"field" => "remark",
			"table" => "bs_products_import",
			"with" => "code"
		),
		array(
			"field" => "product_type_id",
			"table" => "bs_products",
			"with" => "id"
		),
		array(
			"field" => "supplier_id",
			"table" => "bs_suppliers",
			"with" => "id"
		)
	),
	"where" => "bs_incoming_plans.status=1"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
