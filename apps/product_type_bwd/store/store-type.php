<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_products_type.id",
        "code" => "bs_products_type.code",
		"name" => "bs_products_type.name",
        "type" => "bs_products_type.type",
        "product_id" => "bs_products_type.product_id",
        "created" => "bs_products_type.created",
        "name_product" => "bs_products_bwd.name",
        "user" => "bs_products_type.user",
        "status" => "bs_products_type.status",
        "user" 			=> "os_users.display",
	);

	$table = array(
		"index" => "id",
		"name" => "bs_products_type",
        "join" => array(
			array(
				"field" => "product_id",
				"table" => "bs_products_bwd",
				"with" => "id"		
			),array(
				"field" => "user",
				"table" => "os_users",
				"with" => "id"
			)
		)
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
