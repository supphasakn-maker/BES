<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_payments.id",
		"datetime" => "bs_payments.datetime",
		"date" => "DATE(bs_payments.datetime)",
		"time" => "TIME(bs_payments.datetime)",
		"date_active" => "bs_payments.date_active",
		"payment" => "bs_payments.payment",
		"customer_id" => "bs_payments.customer_id",
		"customer_bank" => "bs_payments.customer_bank",
		"bank_id" => "bs_payments.bank_id",
		"ref" => "bs_payments.ref",
		"amount" => "FORMAT(bs_payments.amount,2)",
		"created" => "bs_payments.created",
		"updated" => "bs_payments.updated",
		"approved" => "bs_payments.approved",
		"status" => "bs_payments.status",
		"approver" => "bs_payments.approver",
		"customer" => "bs_customers.name",
	);
	
	$where = '';
	
	if(isset($_GET['date_from'])){
		$where .= " AND (bs_payments.datetime BETWEEN '".$_GET['date_from']." 00:00:00' AND '".$_GET['date_to']." 23:59:59')";
		
	}

	$table = array(
		"index" => "id",
		"name" => "bs_payments",
		"join" => array(
			array(
				"field" => "approver",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "customer_id",
				"table" => "bs_customers",
				"with" => "id"
			)
		),
		"where" => "bs_payments.status > -1".$where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
