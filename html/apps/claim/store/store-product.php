<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_claims.id",
		"code" => "bs_claims.code",
		"type" => "bs_claims.type",
		"created" => "bs_claims.created",
		"updated" => "bs_claims.updated",
		"date_claim" => "bs_claims.date_claim",
		"order_id" => "bs_claims.order_id",
		"issue" => "bs_claims.issue",
		"amount" => "bs_claims.amount",
		"pack_problem" => "bs_claims.pack_problem",
		"pack_claim" => "bs_claims.pack_claim",
		"detail" => "bs_claims.detail",
		"status" => "bs_claims.status",
		"submitted" => "bs_claims.submitted",
		"approved" => "bs_claims.approved",
		"approver_id" => "bs_claims.approver_id",
		"rejected" => "bs_claims.rejected",
		"solved" => "bs_claims.solved",
		"solver_id" => "bs_claims.solver_id",
		"closed" => "bs_claims.closed",
		"user_id" => "bs_claims.user_id",
		"org_name" => "bs_claims.org_name",
		"contact_issuer" => "bs_claims.contact_issuer",
		"contact_sender" => "bs_claims.contact_sender",
		"contact_sales" => "bs_claims.contact_sales",
		"product_id" => "bs_claims.product_id",
		"product_name" => "bs_products.name",
		"order_code" => "bs_orders.code"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_claims",
		"join" => array(
			array(
				"field" => "product_id",
				"table" => "bs_products",
				"with" => "id"
			),
			array(
				"field" => "order_id",
				"table" => "bs_orders",
				"with" => "id"
			)
		),
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>
