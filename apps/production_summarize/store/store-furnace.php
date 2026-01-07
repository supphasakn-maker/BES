<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions_furnace.id",
		"date" => "bs_productions_furnace.date",
        "furnace" => "bs_productions_furnace.furnace",
        "time_start" => "bs_productions_furnace.time_start",
        "time_end" => "bs_productions_furnace.time_end",
        "crucible" => "bs_productions_furnace.crucible",
        "amount" => "bs_productions_furnace.amount",
        "remark" => "bs_productions_furnace.remark",
		"user" => "bs_productions_furnace.user",
		"status" => "bs_productions_furnace.status",
	);


	$table = array(
		"index" => "id",
		"name" => "bs_productions_furnace",
		"where" => "bs_productions_furnace.round = ".$_GET['production_id']
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
