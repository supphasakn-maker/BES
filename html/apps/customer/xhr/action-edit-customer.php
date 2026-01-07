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

if ($dbc->HasRecord("bs_customers", "name = '" . $_POST['name'] . "' AND id != " . $_POST['id'])) {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Customer Name is already exist.'
	));
} else 	if ($_POST['phone'] == "") {
	echo json_encode(array(
		'success' => false,
		'msg' => 'Pleser input phone'
	));
} else {
	$data = array(
		'name' => addslashes($_POST['name']),
		"#gid" => $_POST['gid'],
		"contact" => $_POST['contact'],
		"phone" => $_POST['phone'],
		"fax" => $_POST['fax'],
		"email" => $_POST['email'],
		'shipping_address' => addslashes($_POST['shipping_address']),
		'billing_address' => addslashes($_POST['billing_address']),
		"remark" => addslashes($_POST['remark']),
		"comment" => addslashes($_POST['comment']),
		"#default_sales" => $_POST['default_sales'],
		"default_payment" => $_POST['default_payment'],
		"default_bank" => $_POST['default_bank'],
		"default_vat_type" => $_POST['default_vat_type'],
		"default_pack" => $_POST['default_pack'],
		'#updated' => 'NOW()',
		"org_name" => addslashes($_POST['org_name']),
		"org_taxid" => $_POST['org_taxid'],
		"org_branch" => $_POST['org_branch'],
		"org_address" => addslashes($_POST['billing_address']),
		"new_cus" => $_POST['new_cus'],
		"date_newcus" => $_POST['date_newcus'],
		"#coa" => $_POST['coa'],
		"po" => $_POST['po'] != "" ? $_POST['po'] : "NULL",
		"contact_coc" => $_POST['contact_coc'] != "" ? $_POST['contact_coc'] : "NULL",
		"org_name_coc" => addslashes($_POST['org_name_coc']),
		"address_coc" => addslashes($_POST['address_coc']),
		"certificate_number" => $_POST['certificate_number'] != "" ? $_POST['certificate_number'] : "NULL",
		"certificate_coc" => $_POST['certificate_coc'] != "" ? $_POST['certificate_coc'] : "NULL",
		"export" => ($_POST['export'] != "") ? $_POST['export'] : "NULL",
		"signature" => ($_POST['signature'] != "") ? $_POST['signature'] : "NULL",
		"silvernow_no" => $_POST['silvernow_no'] != "" ? $_POST['silvernow_no'] : "NULL"

	);
	if ($_POST['date_po'] == "") {
		$data['#date_po'] = "NULL";
	} else {
		$data['date_po'] = $_POST['date_po'];
	}

	if ($dbc->Update("bs_customers", $data, "id=" . $_POST['id'])) {
		echo json_encode(array(
			'success' => true
		));
		$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $_POST['id']);
		$os->save_log(0, $_SESSION['auth']['user_id'], "customer-edit", $_POST['id'], array("bs_customers" => $customer));
	} else {
		echo json_encode(array(
			'success' => false,
			'msg' => "No Change"
		));
	}
}

$dbc->Close();
