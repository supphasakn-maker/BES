<?php
session_start();
include_once "../../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../../../include/iface.php";
include_once "../../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_scrap", "เพิ่มเศษ");
$modal->initiForm("form_add_scrap");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.production_prepare.prepare.add_scrap()")
));

$modal->SetVariable(array(
	array("id", $_POST['id'])
));

$production = $dbc->GetRecord("bs_productions", "*", "id=" . $_POST['id']);

$blueprint = array(
	array(
		array(
			"type" => "combobox",
			"name" => "pack_name",
			"caption" => "ประเภทเศษ   ",
			"source" => array("เม็ดเสียรอการผลิต", "เม็ดเสียรอการ Refine"),
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "product_id",
			"type" => "comboboxdb",
			"caption" => "Products",
			"readonly" => "readonly",
			"source" => array(
				"table" => "bs_products",
				"value" => "id",
				"name" => "name",

			),
			"value" => $production['product_id']
		)
	),
	array(
		array(
			"name" => "round",
			"caption" => "รอบ",
			"value" => $_REQUEST['id'],
			"readonly" => "readonly",
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "weight_expected",
			"type" => "number",
			"caption" => "น้ำหนัก",
			"flex" => 4
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
