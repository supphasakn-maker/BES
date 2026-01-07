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

$date = $_POST['bank_date'];

if (empty($date)) {
	$date = NULL;
}

$data = array(
	"#import_id" => $_POST['import_id'],
	"#updated" => "NOW()",
	"import_date" => $_POST['import_date'],
	"import_brand" => $_POST['import_brand'],
	"import_lot" => $_POST['import_lot'],
	"#amount" => $_POST['amount'],
	"#rate_pmdc" => $_POST['rate_pmdc'],
	"factory" => $_POST['factory'],
	"#product_type_id" => $_POST['product_type_id'],
	"coa" => $_POST['coa'],
	"country" => $_POST['country'],
	"coc" => $_POST['coc'],
	"remark" => $_POST['remark'],
	"brand" => $_POST['brand'],
	"#supplier_id" => $_POST['supplier_id'] != "" ? $_POST['supplier_id'] : "NULL",
	"#usd" =>  $_POST['usd'] != "" ? $_POST['usd'] : "NULL",
);
$dataround = array(
	"#import_id" => $_POST['import_id'],
	"#created" => "NOW()",
	"#updated" => "NOW()",
	"#user_id" => $os->auth['id'],
	"import_date" => $_POST['import_date'],
	"import_brand" => $_POST['import_brand'],
	"import_lot" => $_POST['import_lot'],
	"#amount" => $_POST['amount'],
	"#amount_balance" => $_POST['amount'],
	"#rate_pmdc" => $_POST['rate_pmdc'],
	"factory" => $_POST['factory'],
	"#product_type_id" => $_POST['product_type_id'],
	"coa" => $_POST['coa'],
	"remark" => $_POST['remark'],
	"brand" => $_POST['brand'],
	"#parent" => "NULL",
);

$datasigmagin =  array(
	"date" => $_POST['import_date'],
	"#amount" => $_POST['amount'],
	"#rate_pmdc" => $_POST['rate_pmdc'],
	"#transfer" => $_POST['usd'],
	"#import_id" => $_POST['import_id']
);

if ($_POST['bank_date'] == "") {
	$data['#bank_date'] = "NULL";
} else {
	$data['bank_date'] = $_POST['bank_date'];
}

if ($dbc->Update("bs_incoming_plans", $data, "id=" . $_POST['id'])) {
	echo json_encode(array(
		'success' => true
	));
	if ($_POST['supplier_id'] == "1") {
		$dbc->Update("bs_smg_receiving", $datasigmagin, "import_id=" . $_POST['import_id']);
	} else {
	}

	if ($_POST['supplier_id'] == "6") {
		$dbc->Update("bs_smg_stx_receiving", $datasigmagin, "import_id=" . $_POST['import_id']);
	} else {
	}
	$dbc->Update("bs_productions_round", $dataround, "import_id=" . $_POST['import_id']);
	$plan = $dbc->GetRecord("bs_incoming_plans", "*", "id=" . $_POST['id']);
	$os->save_log(0, $_SESSION['auth']['user_id'], "plan-edit", $_POST['id'], array("plans" => $plan));
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => "No Change"
	));
}


$dbc->Close();
