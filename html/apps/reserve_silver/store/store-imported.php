<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_reserve_silver.id",
		"lock_date" => "bs_reserve_silver.lock_date",
		"supplier" => "bs_suppliers.name",
		"discount" => "bs_reserve_silver.discount",
		"weight_lock" => "bs_reserve_silver.weight_lock",
		"weight_actual" => "bs_reserve_silver.weight_actual",
		"weight_fixed" => "bs_reserve_silver.weight_fixed",
		"weight_pending" => "bs_reserve_silver.weight_pending",
		"defer" => "bs_reserve_silver.defer",
		"bar" => "bs_reserve_silver.bar",
		"type" => "bs_reserve_silver.type",
		"created" => "bs_reserve_silver.created",
		"updated" => "bs_reserve_silver.updated",
		"user" => "os_users.display",
		"import_id" => "bs_reserve_silver.import_id",
	);
	$where = "bs_reserve_silver.import_id IS NOT NULL";
	
	if(isset($_GET['date_from'])){
		$where .= " AND ((bs_reserve_silver.lock_date BETWEEN '".$_GET['date_from']."' AND '".$_GET['date_to']."')";
		$where .= " OR bs_reserve_silver.lock_date IS NULL) ";
	}

	$table = array(
		"index" => "id",
		"name" => "bs_reserve_silver",
		"join" => array(
			array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			),array(
				"field" => "supplier_id",
				"table" => "bs_suppliers",
				"with" => "id"
			)
		),"where" => $where
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
