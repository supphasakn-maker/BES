<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_payment_deposits.id",
		"customer_id" => "bs_payment_deposits.customer_id",
		"payment_id" => "bs_payment_deposits.payment_id",
		"amount" => "bs_payment_deposits.amount",
		"status" => "bs_payment_deposits.status",
		"date" => "DATE(bs_payments.datetime)",
		"remain" => "bs_payment_deposits.amount",
	);

	$where = '';
	
	if(isset($_GET['customer_id'])){
		$where .= " AND bs_payment_deposits.customer_id = ".$_GET['customer_id'];
	}

	$table = array(
		"index" => "id",
		"name" => "bs_payment_deposits",
		"join" => array(
			array(
				"field" => "payment_id",
				"table" => "bs_payments",
				"with" => "id"
			)
		),
		"where" => "bs_payment_deposits.status > 0".$where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
