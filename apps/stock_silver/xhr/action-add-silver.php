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


if ($_POST['code'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'โปรดใส่หมายเลขแท่ง'
	));
} else {

	$data = array(
		'#id' => "DEFAULT",
		'code' => $_POST['code'],
		'customer_po' => $_POST['customer_po'],
		'pack_name' => $_POST['pack_name'],
		'pack_type' => $_POST['pack_type'],
		'#weight_actual' => $_POST['weight_actual'],
		'#weight_expected' => $_POST['weight_expected'],
		'#status' => 0,
		'stock' => $_POST['stock'],
		'submited' => $_POST['date'],
		'#product_id' => 2,
		'#created' => 'NOW()',
		'#supplier_id' => 14


	);

	$data2 = array(
		'#id' => "DEFAULT",
		"#production_id" => "NULL",
		'code' => $_POST['code'],
		'pack_name' => $_POST['pack_name'],
		'pack_type' => $_POST['pack_type'],
		'#weight_actual' => $_POST['weight_actual'],
		'#weight_expected' => $_POST['weight_expected'],
		"#parent" => "NULL",
		"#status" => 0,
		"#delivery_id" => "NULL",
		'#created' => 'NOW()'

	);

	if ($dbc->Insert("bs_stock_silver", $data)) {
		$silver_id = $dbc->GetID();
		echo json_encode(array(
			'success' => true,
			'msg' => $silver_id
		));

		$dbc->Insert("bs_packing_items", $data2);

		$silver = $dbc->GetRecord("bs_stock_silver", "*", "id=" . $silver_id);
		$os->save_log(0, $_SESSION['auth']['user_id'], "bs_stock_silver-add", $silver_id, array("bs_stock_silver" => $silver));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "Insert Error"
		));
	}
}

$dbc->Close();
