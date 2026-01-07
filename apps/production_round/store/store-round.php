<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions_round.id",
		"import_id" => "bs_productions_round.import_id",
		"created" => "bs_productions_round.created",
		"updated" => "bs_productions_round.updated",
		"user_id" => "bs_productions_round.user_id",
		"import_date" => "bs_productions_round.import_date",
		"import_brand" => "bs_productions_round.import_brand",
		"import_lot" => "bs_productions_round.import_lot",
		"amount" => "bs_productions_round.amount",
		"user" => "os_users.name",
		"coa" => "bs_productions_round.coa",
		"remark" => "bs_products_import.name",
		"parent" => "bs_productions_round.parent",
		"status" => "bs_productions_round.status"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_productions_round",
		"join" => array(
			array(
				"field" => "user_id",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "remark",
				"table" => "bs_products_import",
				"with" => "code"
			)
		)
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
