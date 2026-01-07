<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_purchase_usd.id",
		"bank" => "bs_purchase_usd.bank",
		"type" => "bs_purchase_usd.type",
		"amount" => "FORMAT(bs_purchase_usd.amount,2)",
		"amount_value" => "bs_purchase_usd.amount",
		"net_amount" => "bs_purchase_usd.amount",
		"rate_exchange" => "FORMAT(bs_purchase_usd.rate_exchange,2)",
		"thb" => "FORMAT(bs_purchase_usd.amount*bs_purchase_usd.rate_exchange,2)",
		"date" => "bs_purchase_usd.date",
		"comment" => "bs_purchase_usd.comment",
		"method" => "bs_purchase_usd.method",
		"ref" => "bs_purchase_usd.ref",
		"user" => "bs_purchase_usd.user",
		"status" => "bs_purchase_usd.status",
		"confirm" => "bs_purchase_usd.confirm",
		"created" => "bs_purchase_usd.created",
		"updated" => "bs_purchase_usd.updated",
		"parent" => "bs_purchase_usd.parent",
		"bank_date" => "bs_purchase_usd.bank_date",
		"premium_start" => "bs_purchase_usd.premium_start",
		"premium" => "bs_purchase_usd.premium",
		"transfer_id" => "bs_purchase_usd.transfer_id",
		"fw_contract_no" => "bs_purchase_usd.fw_contract_no",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_purchase_usd",
		"where" => "bs_purchase_usd.bank LIKE '".$_GET['bank']."' AND transfer_id IS NULL"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
