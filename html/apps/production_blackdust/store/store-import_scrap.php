<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id" => "bs_scrap_items.id",
	"production_id" => "bs_scrap_items.production_id",
	"code" => "bs_scrap_items.code",
	"weight_expected" => "bs_scrap_items.weight_expected",
	"weight_actual" => "bs_scrap_items.weight_actual",
	"parent" => "bs_scrap_items.parent",
	"status" => "bs_scrap_items.status",
	"delivery_id" => "bs_scrap_items.delivery_id",
	"pack_name" => "bs_scrap_items.pack_name",
	"created" => "bs_scrap_items.created",
	"round" => "bs_productions.round",
	"product_id" => "bs_scrap_items.product_id",
	"name" => "bs_products.name",
);

$table = array(
	"index" => "id",
	"name" => "bs_scrap_items",
	"join" => array(
		array(
			"field" => "production_id",
			"table" => "bs_productions",
			"with" => "id"
		),
		array(
			"field" => "product_id",
			"table" => "bs_products",
			"with" => "id"
		)
	),
	"where" => "bs_scrap_items.status > -1 AND bs_scrap_items.pack_name = 'เม็ดเสียรอการ Refine'"
);

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();

$data = $dbc->GetResult();
for ($i = 0; $i < count($data['aaData']); $i++) {
	$counter = $dbc->GetRecord("bs_scrap_items", "COUNT(id)", "parent = " . $data['aaData'][$i]['id']);
	$data['aaData'][$i]['children'] = $counter[0];
}
echo json_encode($data);

$dbc->Close();
