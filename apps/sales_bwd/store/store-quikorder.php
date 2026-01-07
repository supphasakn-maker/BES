<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_orders_bwd.id",
		"code" => "bs_orders_bwd.code",
		"customer_name" => "bs_orders_bwd.customer_name",
		"phone" => "bs_orders_bwd.phone",
		"platform" => "bs_orders_bwd.platform",
		"date" => "bs_orders_bwd.date",	
		"sales" => "os_users.display",
		"user" => "bs_orders_bwd.user",
		"type" => "bs_orders_bwd.type",
		"parent" => "bs_orders_bwd.parent",
		"created" => "bs_orders_bwd.created",
		"updated" => "bs_orders_bwd.updated",
		"amount" => "FORMAT(bs_orders_bwd.amount,4)",
		"price" => "FORMAT(bs_orders_bwd.price,2)",
		"discount_type" => "bs_orders_bwd.discount_type",
		"discount" => "FORMAT(bs_orders_bwd.discount,2)",
		"net" => "FORMAT(bs_orders_bwd.net,2)",
		"total" => "FORMAT(bs_orders_bwd.total,2)",		
		"delivery_date" => "bs_orders_bwd.delivery_date",
		"delivery_time" => "bs_orders_bwd.delivery_time",
		"lock_status" => "bs_orders_bwd.lock_status",
		"status" => "bs_orders_bwd.status",
		"shipping_address" => "bs_orders_bwd.shipping_address",
		"billing_address" => "bs_orders_bwd.billing_address",
		"shipping" => "bs_orders_bwd.shipping",
		"engrave" => "bs_orders_bwd.engrave",
		"font" => "bs_orders_bwd.font",
		"carving" => "bs_orders_bwd.carving",
		"billing_id" => "bs_orders_bwd.billing_id",
		"default_bank" => "bs_orders_bwd.default_bank",
		"info_payment" => "bs_orders_bwd.info_payment",
		"info_contact" => "bs_orders_bwd.info_contact",
		"delivery_id" => "bs_orders_bwd.delivery_id",
		"remove_reason" => "bs_orders_bwd.remove_reason",
		"product_type" => "bs_orders_bwd.product_type",
		"product_id" => "bs_orders_bwd.product_id",
		"delivery_code" => "bs_deliveries_bwd.code"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_orders_bwd",
		"join" => array(
			array(
				"field" => "sales",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "delivery_id",
				"table" => "bs_deliveries_bwd",
				"with" => "id"
			)
		),
        "where" => "DATE(bs_orders_bwd.created) LIKE '".date("Y-m-d")."' AND (bs_orders_bwd.status > 0)"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
