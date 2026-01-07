<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" 			=> "bs_spot_usd_splited.id",
		"purchase_id" 	=> "bs_spot_usd_splited.purchase_id",
		"transfer_id" 	=> "bs_spot_usd_splited.transfer_id",
		"amount" 		=> "bs_spot_usd_splited.amount",
		"created" 		=> "bs_spot_usd_splited.created",
		"updated"		=> "bs_spot_usd_splited.updated",
		"remark" 		=> "bs_spot_usd_splited.remark"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_spot_usd_splited"
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
