<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);


$data = array(
	"#customer_id" => "NULL",
	"order_id" => "NULL",
	"#created" => "NULL",
	"#status" => 1
);

$dbc->Update("bs_coa_run", $data, "id=" . $_POST['id']);


echo json_encode(array(
	'success' => true
));


$dbc->Close();
