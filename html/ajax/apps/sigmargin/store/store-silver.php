<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_smg_trade .id",
		"date" => "bs_smg_trade .date",
		"type" => "bs_smg_trade .type",
		"purchase_type" => "bs_smg_trade .purchase_type",
		"amount" => "bs_smg_trade .amount",
		"rate_spot" => "bs_smg_trade .rate_spot",
		"rate_pmdc" => "bs_smg_trade .rate_pmdc",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_trade ",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
