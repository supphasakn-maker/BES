<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions_scale.id",
        "date" => "bs_productions_scale.date",
        "scale" => "bs_productions_scale.scale",
        "approve_scale" => "bs_productions_scale.approve_scale",
		"approve_packing" => "bs_productions_scale.approve_packing",
		"approve_check" => "bs_productions_scale.approve_check",
        "remark" => "bs_productions_scale.remark",
		"status" => "bs_productions_scale.status",
	);

	
	$table = array(
		"index" => "id",
		"name" => "bs_productions_scale",
		"where" => "bs_productions_scale.round = ".$_GET['production_id']
	);
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
