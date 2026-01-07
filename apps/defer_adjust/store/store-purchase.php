<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" 			=> "bs_adjust_purchase.id",
		"supplier_id"	=> "bs_adjust_purchase.supplier_id",
		"supplier" 		=> "bs_suppliers.name",
        "date" 		=> "bs_adjust_purchase.date",
		"amount" => "bs_adjust_purchase.amount",
        "usd" 		=> "bs_adjust_purchase.usd"

	);

	$table = array(
		"index" => "id",
		"name" => "bs_adjust_purchase",
		"join" => array(
			array(
				"field" => "supplier_id",
				"table" => "bs_suppliers",
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
