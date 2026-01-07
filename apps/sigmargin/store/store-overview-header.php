
<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"receive_id" => "bs_smg_receiving.id",
		"receive_date" => "bs_smg_receiving.date",
		"receive_amount" => "bs_smg_receiving.amount",
		"receive_rate_pmdc" => "bs_smg_receiving.rate_pmdc",
		"payment_id" => "bs_smg_payment.id",
		"payment_date" => "bs_smg_payment.date",
		"payment_type" => "bs_smg_payment.type",
		"payment_usd" => "bs_smg_payment.amount_usd",
		"payment_rate_pmdc" => "bs_smg_payment.rate_pmdc",
		"payment_total" => "bs_smg_payment.amount_total",
		"trade_id" => "bs_smg_trade.id",
		"trade_type" => "bs_smg_trade.type",
		"trade_purchase_type" => "bs_smg_trade.purchase_type",
		"trade_amount" => "bs_smg_trade.amount",
		"trade_rate_spot" => "bs_smg_trade.rate_spot",
		"trade_rate_pmdc" => "bs_smg_trade.rate_pmdc"
		
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_receiving",
		"join" => array(
			array(
				"field" => "date",
				"table" => "bs_smg_payment",
				"with" => "date",
				"where" => "MONTH(date) = MONTH(CURRENT_DATE())"
			),
			array(
				"join" => "bs_smg_payment",
				"field" => "date",
				"table" => "bs_smg_trade",
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
