<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions_oven.id",
		"date" => "bs_productions_oven.date",
        "oven" => "bs_productions_oven.oven",
        "time_start" => "bs_productions_oven.time_start",
        "time_end" => "bs_productions_oven.time_end",
        "temp" => "bs_productions_oven.temp",
        "remark" => "bs_productions_oven.remark",
		"user" => "bs_productions_oven.user",
		"status" => "bs_productions_oven.status",
	);

	
	$table = array(
		"index" => "id",
		"name" => "bs_productions_oven",
		"where" => "bs_productions_oven.round = ".$_GET['production_id']
	);
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
