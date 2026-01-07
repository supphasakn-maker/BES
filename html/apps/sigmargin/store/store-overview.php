
<?php
	// session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$key_Date = $_POST['key_date'];	

	$dbc = new datastore;
	$dbc->Connect();
	
	$columns = array(
		"id" => "bs_smg_receiving .id",
		"date" => "bs_smg_receiving .date",
		"amount" => "bs_smg_receiving .amount",
		"rate_pmdc" => "bs_smg_receiving .rate_pmdc"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_smg_receiving ",
		"where" => "'$key_Date'"
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	
	$dbc->Processing();
	$res_data = $dbc->getResult();
	echo json_encode($res_data['aaData']);	

	$dbc->Close();

?>
