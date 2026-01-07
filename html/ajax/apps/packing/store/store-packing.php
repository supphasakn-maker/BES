<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_packings.id",
		"production_id" => "bs_packings.production_id",
		"round" => "bs_packings.round",
		"date" => "bs_packings.date",
		"time" => "bs_packings.time",
		"weight_peritem" => "bs_packings.weight_peritem",
		"total_item" => "bs_packings.total_item",
		"total_weight" => "bs_packings.total_weight",
		"size" => "bs_packings.size",
		"remark" => "bs_packings.remark",
		"created" => "bs_packings.created",
		"updated" => "bs_packings.updated",
		"approver_weight" => "bs_packings.approver_weight",
		"approver_general" => "bs_packings.approver_general",
		"status" => "bs_packings.status",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_packings",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
