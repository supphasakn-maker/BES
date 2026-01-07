<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_transfers.id",
		"bank" => "bs_transfers.bank",
		"transfer_date" => "bs_transfers.transfer_date",
		"type" => "bs_transfers.type",
		"fixed_value" => "bs_transfers.fixed_value",
		"nonfixed_value" => "bs_transfers.nonfixed_value",
		"rate_counter" => "bs_transfers.rate_counter",
		"total_transfer" => "bs_transfers.total_transfer",
		"net_good_value" => "bs_transfers.net_good_value",
		"deposit" => "bs_transfers.deposit",
		"created" => "bs_transfers.created",
		"updated" => "bs_transfers.updated",
		"supplier" => "bs_suppliers.name",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_transfers",
		"join" => array(
			array(
				"field" => "supplier_id",
				"table"=> "bs_suppliers",
				"with" => "id"
			)
		)
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
