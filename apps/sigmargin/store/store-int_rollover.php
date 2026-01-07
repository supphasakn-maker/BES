<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_smg_rate_rollover.id", 
		"date" => "bs_smg_rate_rollover.date",
		"rate_short" => "bs_smg_rate_rollover.rate_short",
		"rate" => "bs_smg_rate_rollover.rate",
        "interest" => "bs_smg_rate_rollover.interest"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_rate_rollover",
		//"where" => "bs_smg_rate.date LIKE '".$_GET['date']."'"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
