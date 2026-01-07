<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_bank_statement.id",
		"bank_id" => "bs_bank_statement.bank_id",
		"date" => "bs_bank_statement.date",
		"type" => "bs_bank_statement.type",
		"amount" => "bs_bank_statement.amount",
		"balance" => "bs_bank_statement.balance",
		"narrator" => "bs_bank_statement.narrator",
		"payment_id" => "bs_bank_statement.payment_id",
		"status" => "bs_bank_statement.status",
		"created" => "bs_bank_statement.created",
		"updated" => "bs_bank_statement.updated",
		"approved" => "bs_bank_statement.approved",
		"transfer_to" => "bs_bank_statement.transfer_to"
	);
	
	

	$table = array(
		"index" => "id",
		"name" => "bs_bank_statement",
		"where" => "bs_bank_statement.bank_id = ".$_GET['bank_id']." AND bs_bank_statement.date = '".$_GET['bank_date']."'"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	
	
	$data = $dbc->GetResult();
	
	$debit = $dbc->GetRecord("bs_bank_statement","SUM(amount)","type = 1 AND bank_id = ".$_GET['bank_id']." AND date = '".$_GET['bank_date']."'");
	$credit = $dbc->GetRecord("bs_bank_statement","SUM(amount)","type = 2 AND bank_id = ".$_GET['bank_id']." AND date = '".$_GET['bank_date']."'");
	
	$data['total'] = array(
		"debit" => $debit[0],
		"credit" => $credit[0]
	);
	
	echo json_encode($data);

	$dbc->Close();

?>
