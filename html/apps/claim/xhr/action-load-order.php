<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);

	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['order_id']);
	$customer = $dbc->GetRecord("bs_customers","*","id=".$order['customer_id']);
	
	//$sales = $dbc->GetRecord("bs_sales","*","id=".$order['customer_id']);


	echo json_encode(array(
		'order'=>$order,
		'customer'=>$customer,
		'product_id'=>$order,
	));

	$dbc->Close();
?>
