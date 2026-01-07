<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_smg_receiving.id",
		"date" => "bs_smg_receiving.date",
		"amount" => "bs_smg_receiving.amount",
		"rate_pmdc" => "bs_smg_receiving.rate_pmdc",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_receiving",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
