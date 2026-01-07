<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/datastore.php";

	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new datastore;
	$dbc->Connect();

	$columns = array(
		"id" => "bs_customers.id",

		"name" => "bs_customers.name",
		"gid" => "bs_customers.gid",
		"contact" => "bs_customers.contact",
		"phone" => "bs_customers.phone",
		"fax" => "bs_customers.fax",
		"email" => "bs_customers.email",
		"shipping_address" => "bs_customers.shipping_address",
		"billing_address" => "bs_customers.billing_address",
		"remark" => "bs_customers.remark",
		"comment" => "bs_customers.comment",
		"default_sales" => "bs_customers.default_sales",
		"default_payment" => "bs_customers.default_payment",
		"default_bank" => "bs_customers.default_bank",
		"default_vat_type" => "bs_customers.default_vat_type",
		"default_pack" => "bs_customers.default_pack",
		
		"created" => "bs_customers.created",
		"updated" => "bs_customers.updated",
		"org_name" => "bs_customers.org_name",
		"org_taxid" => "bs_customers.org_taxid",
		"org_branch" => "bs_customers.org_branch",
		"org_address" => "bs_customers.org_address"
	);

	$table = array(
		"index" => "id",
		"name" => "bs_customers",
	);

	$dbc->SetParam($table,$columns,$_GET['order'],$_GET['columns'],$_GET['search']);
	$dbc->SetLimit($_GET['length'],$_GET['start']);
	$dbc->Processing();
	echo json_encode($dbc->GetResult());

	$dbc->Close();

?>


