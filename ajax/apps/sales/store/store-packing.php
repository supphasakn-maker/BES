<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_stock_prepare.id",
		"user" => "os_users.display",
		"created" => "bs_stock_prepare.created",
		"updated" => "bs_stock_prepare.updated",
		"delivery_date" => "bs_stock_prepare.delivery_date",
		"prepare_date" => "bs_stock_prepare.prepare_date",
		"status" => "bs_stock_prepare.status",
		"approved" => "bs_stock_prepare.approved",
		"amount" => "bs_stock_prepare.amount",
		"comment" => "bs_stock_prepare.comment",
		"info_amount" => "bs_stock_prepare.info_amount",
		"info_mine" => "bs_stock_prepare.info_mine",
		"status_show" => "bs_stock_prepare.status_show"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_stock_prepare",
		"join" => array(
			array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			)
		),
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
