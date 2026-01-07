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

$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_POST['id']);




$data = array(
	"#id" => "DEFAULT",
	"#production_id" => $_POST['id'],
	"#weight_actual" => $_POST['weight_expected'],
	"#weight_expected" => $_POST['weight_expected'],
	"#parent" => "NULL",
	"#status" => -2,
	"#delivery_id" => "NULL",
	"pack_type" => "เศษ",
	"pack_name" => "เศษเสียรอการผลิตนำเข้า",
	"#created" => "NOW()",
	"#datecancel" => "NOW()"
);

if ($dbc->Insert("bs_scrap_items", $data)) {
	$pack_combine_id = $dbc->GetID();
	echo json_encode(array(
		'success' => true
	));
	$round = sprintf($pack_combine_id);
	$dbc->Update("bs_scrap_items", array("code" => $round), "id=" . $pack_combine_id);

	foreach ($_POST['pack_id'] as $id) {
		$data = array(
			'#status' => -2,
			'#parent' => $pack_combine_id
		);
		$dbc->Update("bs_scrap_items", $data, "id=" . $id);
	}

	$data2 = array(
		'#weight_in_safe' => $_POST['weight_expected']
	);
	$dbc->Update("bs_productions", $data2, "id=" . $_POST['id']);
} else {
	echo json_encode(array(
		'success' => false,
		'msg' => "No Change"
	));
}


$dbc->Close();
