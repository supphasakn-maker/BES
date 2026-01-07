<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/datastore.php";

date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new datastore;
$dbc->Connect();

$columns = array(
	"id"			=> "bs_purchase_usd.id",
	"bank" 			=> "bs_purchase_usd.bank",
	"type" 			=> "bs_purchase_usd.type",
	"amount" 		=> "FORMAT(bs_purchase_usd.amount,4)",
	"rate_exchange" => "FORMAT(bs_purchase_usd.rate_exchange,6)",
	"rate_finance"  => "FORMAT(bs_purchase_usd.rate_finance,6)",
	"date" 			=> "bs_purchase_usd.date",
	"comment" 		=> "bs_purchase_usd.comment",
	"method" 		=> "bs_purchase_usd.method",
	"ref" 			=> "bs_purchase_usd.ref",
	"user" 			=> "os_users.display",
	"created" 		=> "bs_purchase_usd.created",
	"status" 		=> "bs_purchase_usd.status",
	"confirm" 		=> "bs_purchase_usd.confirm",
	"parent" 		=> "bs_purchase_usd.parent",
	"finance" 		=> "bs_purchase_usd.finance",
	"children" 		=> "(SELECT COUNT(id) FROM bs_purchase_usd AS c WHERE c.parent = bs_purchase_usd.id)",
);

$table = array(
	"index" => "id",
	"name" => "bs_purchase_usd",
	"join" => array(
		array(
			"field" => "user",
			"table" => "os_users",
			"with" => "id"
		)
	),
);

if (isset($_GET['where'])) {
	$table['where'] = $_GET['where'];
}

if (isset($table['where'])) {
	$table['where'] .= " AND bs_purchase_usd.transfer_id IS NULL";
} else {
	$table['where'] = " bs_purchase_usd.transfer_id IS NULL";
}

$dbc->SetParam($table, $columns, $_GET['order'], $_GET['columns'], $_GET['search']);
$dbc->SetLimit($_GET['length'], $_GET['start']);
$dbc->Processing();
echo json_encode($dbc->GetResult());

$dbc->Close();
