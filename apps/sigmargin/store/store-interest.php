<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_smg_interest.id",
		"date_start" => "bs_smg_interest.date_start",
		"date_end" => "bs_smg_interest.date_end",
		"interest" => "bs_smg_interest.interest",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_interest",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
