
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
		"cash_id" => "bs_smg_cash.id",
		"cash_date" => "bs_smg_cash.date",
		"cash_amount" => "bs_smg_cash.amount"

	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_rollover",
		"join" => array(
			array(
				"field" => "date",
				"table" => "bs_smg_cash",
				"with" => "date",
				"where" => "MONTH(date) = MONTH(CURRENT_DATE()"
			)
		)
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
