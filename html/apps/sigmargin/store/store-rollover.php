<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_smg_rollover.id",
		"trade" => "bs_smg_rollover.trade",
		"date" => "bs_smg_rollover.date",
		"type" => "bs_smg_rollover.type",
		"amount" => "bs_smg_rollover.amount",
		"rate_spot" => "bs_smg_rollover.rate_spot",
		"entry" => "bs_smg_rollover.entry"
	);  

	$table = array(
		"index" => "id",
		"name" => "bs_smg_rollover",
		// "where" => "bs_smg_rollover.date = '".$_GET['date']."'"
		"where" => "DATE_FORMAT(bs_smg_rollover.date,'%Y-%m') = '".date('Y-m', strtotime($_GET['date']))."'"

	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
