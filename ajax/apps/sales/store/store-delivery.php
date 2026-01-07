<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_deliveries.id",
		"code" => "bs_deliveries.code",
		"order_code" => "bs_orders.code",
		"type" => "bs_deliveries.type",
		"delivery_date" => "bs_deliveries.delivery_date",
		"created" => "bs_deliveries.created",
		"updated" => "bs_deliveries.updated",
		"status" => "bs_deliveries.status",
		"amount" => "FORMAT(bs_deliveries.amount,4)",
		"price" => "FORMAT(bs_orders.price,2)",
		"user" => "bs_deliveries.user",
		"comment" => "bs_deliveries.comment",
		"customer_name" => "bs_orders.customer_name",
		"customer_id" => "bs_orders.customer_id",
		"date" => "bs_orders.date",
		"info_payment" => "bs_orders.info_payment",
		"billing_id" => "bs_deliveries.billing_id",
		"payment_note" => "bs_deliveries.payment_note"
		
		
	);

	$where = '';
	if(isset($_GET['date_from'])){
		
		$where = " AND (bs_deliveries.delivery_date BETWEEN '".$_GET['date_from']." 00:00:00' AND '".$_GET['date_to']." 23:59:59')";
	
	}
	
	
	
	$table = array(
		"index" => "id",
		"name" => "bs_deliveries",
		"join" => array(
			array(
				"field" => "id",
				"table" => "bs_orders",
				"with" => "delivery_id"
			)
		),
		"where" => "bs_orders.status > 0".$where,
		"groupby" => "bs_deliveries.id"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
