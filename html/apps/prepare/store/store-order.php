<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_orders.id",
		"code" => "bs_orders.code",
		"customer_id" => "bs_orders.customer_id",
		"customer_name" => "bs_orders.customer_name",
		"date" => "DATE_FORMAT(bs_orders.date,'%Y-%m-%d')",
		"sales" => "bs_employees.fullname",
		"user" => "os_users.name",
		"type" => "bs_orders.type",
		"parent" => "bs_orders.parent",
		"created" => "bs_orders.created",
		"updated" => "bs_orders.updated",
		"amount" => "bs_orders.amount",
		"price" => "FORMAT(bs_orders.price,2)",
		"vat_type" => "bs_orders.vat_type",
		"vat_amount" => "FORMAT(bs_orders.vat_amount,2)",
		"total" => "FORMAT(bs_orders.total,2)",
		"net" => "FORMAT(bs_orders.net,2)",
		"delivery_date" => "bs_orders.delivery_date",
		"delivery_time" => "bs_orders.delivery_time",
		"lock_status" => "bs_orders.lock_status",
		"status" => "bs_orders.status",
		"comment" => "bs_orders.comment",
		"shipping_address" => "bs_orders.shipping_address",
		"billing_address" => "bs_orders.billing_address",
		"payment" => "bs_orders.payment",
		"rate_spot" => "bs_orders.rate_spot",
		"rate_exchange" => "bs_orders.rate_exchange",
		"billing_id" => "bs_orders.billing_id",
		"packing_id" => "bs_packings.id",
		"delivery_id" => "bs_packings.delivery_id"
	);
	
	
	$where = '';
	if(isset($_GET['date_from'])){
		//$where .= " AND delivery_date BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."'";
	}
	
	
	if(isset($_GET['delivery_date'])){
		$where .= " AND delivery_date BETWEEN '".$_GET['delivery_date']."' AND '".$_GET['delivery_date']."'";
	}
	
	
	
	
	if(isset($_GET['customer_id'])){
		$where .= " AND bs_orders.customer_id = ".$_GET['customer_id'];
	}
	

	$table = array(
		"index" => "id",
		"name" => "bs_orders",
		"join" => array(
			array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "sales",
				"table" => "bs_employees",
				"with" => "id"
			),array(
				"field" => "id",
				"table" => "bs_packings",
				"with" => "order_id"
			)
		),
		"where" => "bs_orders.status != 0".$where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
