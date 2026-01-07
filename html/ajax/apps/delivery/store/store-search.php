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
		"code" => "bs_packing_items.code",
		"pack_name" => "bs_packing_items.pack_name",
		"pack_type" => "bs_packing_items.pack_type",
		"weight_actual" => "bs_packing_items.weight_actual",
		"weight_expected" => "bs_packing_items.weight_expected"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_delivery_pack_items"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
