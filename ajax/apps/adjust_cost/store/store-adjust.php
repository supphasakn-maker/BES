<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_adjust_cost.id",
		"date_adjust" => "bs_adjust_cost.date_adjust",
		"created" => "bs_adjust_cost.created",
		"updated" => "bs_adjust_cost.updated",
		"value_amount" => "bs_adjust_cost.value_amount",
		"value_buy" => "bs_adjust_cost.value_buy",
		"value_sell" => "bs_adjust_cost.value_sell",
		"value_new" => "bs_adjust_cost.value_new",
		"value_profit" => "bs_adjust_cost.value_profit",
		"value_adjust_cost" => "bs_adjust_cost.value_adjust_cost",
		"value_adjust_discount" => "bs_adjust_cost.value_adjust_discount",
		"value_net" => "bs_adjust_cost.value_net",
		"user" => "bs_adjust_cost.user",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_adjust_cost",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
