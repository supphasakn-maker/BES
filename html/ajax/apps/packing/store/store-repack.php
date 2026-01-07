<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_packing_items.id",
		"packing_id" => "bs_packing_items.packing_id",
		"code" => "bs_packing_items.code",
		"weight_expected" => "bs_packing_items.weight_expected",
		"weight_actual" => "bs_packing_items.weight_actual",
		"parent" => "bs_packing_items.parent",
		"status" => "bs_packing_items.status",
		"delivery_id" => "bs_packing_items.delivery_id",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_packing_items",
		"where" => "bs_packing_items.status = 1"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
