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
		"production_id" => "bs_packing_items.production_id",
		"code" => "bs_packing_items.code",
		"weight_expected" => "bs_packing_items.weight_expected",
		"weight_actual" => "bs_packing_items.weight_actual",
		"parent" => "bs_packing_items.parent",
		"status" => "bs_packing_items.status",
		"delivery_id" => "bs_packing_items.delivery_id",
		"pack_type" => "bs_packing_items.pack_type",
		"created" => "bs_packing_items.created",
		"round" => "bs_productions.round",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_packing_items",
		"join" => array(
			array(
				"field" => "production_id",
				"table" => "bs_productions",
				"with" => "id"
			),array(
				"field" => "id",
				"table" => "bs_delivery_pack_items",
				"with" => "item_id"
			)
		),
		"where" => "bs_packing_items.status > -1 AND bs_delivery_pack_items.id IS NULL"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	
	$data = $dbc->GetResult();
	for($i=0;$i<count($data['aaData']);$i++){
		$counter = $dbc->GetRecord("bs_packing_items","COUNT(id)","parent = ".$data['aaData'][$i]['id']);
		$data['aaData'][$i]['children'] = $counter[0];
	}
	echo json_encode($data);

	$dbc->Close();

?>
