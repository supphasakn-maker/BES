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
	'#id' => "DEFAULT",
	"#type_id" => $_POST['type_id'],
	"#product_id" => $_POST['product_id'],
	"remark" => $_POST['remark'],
	"#amount" => $_POST['amount'],
	"#amount2" => $amount2,
	"#amount3" => 0,
	'#created' => 'NOW()',
	'#updated' => 'NOW()',
	"date" => $_POST['date']
);

if ($dbc->Insert("bs_stock_adjusted", $data)) {
	$adjust_id = $dbc->GetID();
	echo json_encode(array(
		'success' => true,
		'msg' => $adjust_id
	));

	$adjust = $dbc->GetRecord("bs_stock_adjusted", "*", "id=" . $adjust_id);
	$os->save_log(0, $_SESSION['auth']['user_id'], "adjust-add", $adjust_id, array("adjusts" => $adjust));
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => "Insert Error"
	));
}

$dbc->Close();
