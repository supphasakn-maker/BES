<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" 			=> "bs_productions_round_splited.id",
		"round_id" 		=> "bs_productions_round_splited.round_id",
		"import_lot" 	=> "bs_productions_round_splited.import_lot",
		"amount" 		=> "bs_productions_round_splited.amount",
		"created" 		=> "bs_productions_round_splited.created",
		"updated"		=> "bs_productions_round_splited.updated",
		"remark" 		=> "bs_productions_round_splited.remark"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_productions_round_splited"
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
