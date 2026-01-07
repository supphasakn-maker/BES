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


$amount = floatval($_POST['amount']);
if ($amount < 0) {
	$amount2 = abs($amount);
} else {
	$amount2 = -$amount;
}

$data = array(
	"#type_id" => $_POST['type_id'],
	"#product_id" => $_POST['product_id'],
	"remark" => $_POST['remark'],
	"#amount" => $_POST['amount'],
	"#amount2" => $amount2,
	"#amount3" => 0,
	'#updated' => 'NOW()',
	"date" => $_POST['date']
);

if ($dbc->Update("bs_stock_adjusted", $data, "id=" . $_POST['id'])) {
	echo json_encode(array(
		'success' => true
	));
	$adjust = $dbc->GetRecord("bs_stock_adjusted", "*", "id=" . $_POST['id']);
	$os->save_log(0, $_SESSION['auth']['user_id'], "adjust-edit", $_POST['id'], array("adjusts" => $adjust));
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => "No Change"
	));
}


$dbc->Close();
