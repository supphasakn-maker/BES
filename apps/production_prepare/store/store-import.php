<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_imports.id",
		"supplier_id" => "bs_imports.supplier_id",
		"supplier_name" => "bs_suppliers.name",
		"amount" => "bs_imports.amount",
		"type" => "bs_imports.type",
		"delivery_date" => "bs_imports.delivery_date",
		"delivery_by" => "bs_imports.delivery_by",
		"comment" => "bs_imports.comment",
		"created" => "bs_imports.created",
		"updated" => "bs_imports.updated",
		"user" => "bs_imports.user",
		"status" => "bs_imports.status",
		"submited" => "bs_imports.submited",
		"production_id" => "bs_imports.production_id",
		"delivery_time" => "bs_imports.delivery_time",
		"delivery_note" => "bs_imports.delivery_note",
		"weight_in" => "bs_imports.weight_in",
		"weight_actual" => "bs_imports.weight_actual",
		"weight_margin" => "bs_imports.weight_margin",
		"bar" => "bs_imports.bar",
		"weight_bar" => "bs_imports.weight_bar",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_imports",
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
		),"where" => "bs_imports.type=0 AND bs_imports.status=0"
	);
	$where = '';
	if(isset($_GET['where'])){
		$table['where']=$_GET['where'];
	}
	
	

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
