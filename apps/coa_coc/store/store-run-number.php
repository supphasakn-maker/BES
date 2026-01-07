<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_coa_run.id",
	"number" => "bs_coa_run.number",
	"number_coc" => "bs_coa_run.number_coc",
	"customer_id" => "bs_coa_run.customer_id",
	"order_id" => "bs_coa_run.order_id",
	"name" => "bs_customers.name",
	"created" => "bs_coa_run.created",
	"status" => "bs_coa_run.status",
);

$five_days_ago = date('Y-m-d', strtotime('-5 days'));
$today = date('Y-m-d', strtotime('+10 days'));

$table = array(
	"index" => "id",
	"name" => "bs_coa_run",
	"join" => array(
		array(
			"field" => "customer_id",
			"table" => "bs_customers",
			"with" => "id"
		)
	),
	"where" => "bs_coa_run.id > 371 AND ((bs_coa_run.created >= '$five_days_ago' AND bs_coa_run.created <= '$today') OR bs_coa_run.created IS NULL)"
);


$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
