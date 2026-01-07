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


if ($dbc->HasRecord("bs_incoming_plans", "import_id = " . $_POST['import_id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Plan Name is already exist.'
	));
} else {
	$data = array(
		"#id" => "DEFAULT",
		"#import_id" => $_POST['import_id'],
		"#created" => "NOW()",
		"#updated" => "NOW()",
		"#user_id" => $os->auth['id'],
		"import_date" => $_POST['import_date'],
		"import_brand" => $_POST['import_brand'],
		"import_lot" => $_POST['import_lot'],
		"#amount" => $_POST['amount'],
		"#rate_pmdc" => $_POST['rate_pmdc'],
		"factory" => $_POST['factory'],
		"#product_type_id" => $_POST['product_type_id'],
		"coa" => $_POST['coa'] != "" ? $_POST['coa'] : "NULL",
		"country" => $_POST['country'] != "" ? $_POST['country'] : "NULL",
		"coc" => $_POST['coc'] != "" ? $_POST['coc'] : "NULL",
		"remark" => $_POST['remark'] != "" ? $_POST['remark'] : "NULL",
		"brand" => $_POST['brand'] != "" ? $_POST['brand'] : "NULL",
		"#parent" => "NULL",
		"#supplier_id" => $_POST['supplier_id'] != "" ? $_POST['supplier_id'] : "NULL",
		"#usd" => $_POST['usd'] != "" ? $_POST['usd'] : "NULL",
		"#status" => 1
	);

	$datasigmagin =  array(
		"#id" => "DEFAULT",
		"date" => $_POST['import_date'],
		"#amount" => $_POST['amount'],
		"#rate_pmdc" => $_POST['rate_pmdc'],
		"#transfer" => $_POST['usd'] != "" ? $_POST['usd'] : "NULL",
		"#import_id" => $_POST['import_id']
	);

	$dataround = array(
		"#id" => "DEFAULT",
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
		"#status" => 1
	);

	if ($dbc->Insert("bs_incoming_plans", $data)) {
		$plan_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $plan_id
		));

		$dbc->Insert("bs_productions_round", $dataround);

		if ($_POST['supplier_id'] == "1") {
			$dbc->Insert("bs_smg_receiving", $datasigmagin);
		}


		if ($_POST['supplier_id'] == "6") {
			$dbc->Insert("bs_smg_stx_receiving", $datasigmagin);
		}

		$plan = $dbc->GetRecord("bs_incoming_plans", "*", "id=" . $plan_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "plan-add", $plan_id, array("plans" => $plan));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
