<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_productions_crucible.id",
        "date" => "bs_productions_crucible.date",
		"round" => "bs_productions_crucible.round",
		"submited" => "bs_productions_crucible.submited",
		"status" => "bs_productions_crucible.status",
	);

	$where = '1';
	
	if(isset($_GET['date_from'])){
		$where .= " AND (bs_productions_crucible.date BETWEEN '".$_GET['date_from']." 00:00:00' AND '".$_GET['date_to']." 23:59:59')";
	}
	$table = array(
		"index" => "id",
		"name" => "bs_productions_crucible",
		"where" => $where
	);
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
