<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_productions_pmr.id",
	"round" => "bs_productions_pmr.round",
	"created" => "bs_productions_pmr.created",
	"updated" => "bs_productions_pmr.updated",
	"user" => "bs_productions_pmr.user",
	"remark" => "bs_productions_pmr.remark",
	"weight_out_packing" => "bs_productions_pmr.weight_out_packing",
	"weight_out_total" => "bs_productions_pmr.weight_out_total",
	"submited" => "bs_productions_pmr.submited",
	"product_id" => "bs_productions_pmr.product_id",
	"product_name" => "bs_products.name",
	"status" => "bs_productions_pmr.status"
);

$where = "1";

if (isset($_GET['date_from'])) {
	$where .= " AND date(bs_productions_pmr.created) BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "' AND bs_productions_pmr.product_id = '3'";
}
$where .= " AND bs_productions_pmr.remark REGEXP '(ส่งเม็ดผลิต|ส่งเม็ด)'";

$table = array(
	"index" => "id",
	"name" => "bs_productions_pmr",
	"join" => array(
		array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		)
	),
	"where" => $where
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
