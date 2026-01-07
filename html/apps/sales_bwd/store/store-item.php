<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_bwd_pack_items.id",
		"delivery_id" => "bs_bwd_pack_items.delivery_id",
		"item_type" => "bs_bwd_pack_items.item_type",
		"item_id" => "bs_bwd_pack_items.item_id",
		"remark" => "bs_bwd_pack_items.remark",
		"mapping" => "bs_bwd_pack_items.mapping",
		"code" => "bs_stock_bwd.code",
		"pack_name" => "bs_stock_bwd.pack_name",
		"pack_type" => "bs_stock_bwd.pack_type",
		"amount" => "bs_stock_bwd.amount",
		"weight_expected" => "bs_stock_bwd.weight_expected"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_bwd_pack_items",
		"join" => array(
			array(
				"field" => "item_id",
				"table" => "bs_stock_bwd",
				"with" => "id"
			)
		)
	);
	
	if(isset($_GET['where'])){
		$table['where'] = $_GET['where'];
	}

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
