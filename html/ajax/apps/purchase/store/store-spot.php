<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" 			=> "bs_purchase_spot.id",
		"supplier_id"	=> "bs_purchase_spot.supplier_id",
		"supplier" 		=> "bs_suppliers.name",
		"type" 			=> "bs_purchase_spot.type",
		"amount" 		=> "FORMAT(bs_purchase_spot.amount,4)",
		"rate_spot" 	=> "FORMAT(bs_purchase_spot.rate_spot,4)",
		"rate_pmdc" 	=> "FORMAT(bs_purchase_spot.rate_pmdc,4)",
		"date" 			=> "bs_purchase_spot.date",
		"value_date" 	=> "bs_purchase_spot.value_date",
		"created" 		=> "bs_purchase_spot.created",
		"updated"	 	=> "bs_purchase_spot.updated",
		"method" 		=> "bs_purchase_spot.method",
		"ref" 			=> "bs_purchase_spot.ref",
		"user" 			=> "os_users.display",
		"status" 		=> "bs_purchase_spot.status",
		"confirm" 		=> "bs_purchase_spot.confirm",
		"order_id" 		=> "bs_purchase_spot.order_id",
		"comment" 		=> "bs_purchase_spot.comment",
		"parent" 		=> "bs_purchase_spot.parent",
		"currency" 		=> "bs_purchase_spot.currency",
		"THBValue" 		=> "bs_purchase_spot.THBValue"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_purchase_spot",
		"join" => array(
			array(
				"field" => "supplier_id",
				"table" => "bs_suppliers",
				"with" => "id"
			),array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			)
		)
	);
	
	
	if(isset($_GET['where'])){
		$table['where'] = $_GET['where'];
	}
	
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
