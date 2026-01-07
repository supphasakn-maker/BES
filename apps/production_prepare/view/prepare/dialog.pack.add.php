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
$modal->setModel("dialog_add_pack", "Add Package");
$modal->initiForm("form_addpack");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.production_prepare.prepare.add_pack()")
));

$modal->SetVariable(array(
	array("id", $_POST['id'])
));


$aPacking = json_decode($os->load_variable("aPacking", "json"), true);

$select_packtype = '<select name="pack_name" class="form-control mr-2">';
foreach ($aPacking as $pack) {
	$readonly = isset($pack['readonly']) ? $pack['readonly'] : true;
	$select_packtype .=  '<option data-value="' . $pack['value'] . '" data-readonly="' . ($readonly ? "true" : "false") . '">' . $pack['name'] . '</option>';
}
$select_packtype .= '</select>';


$blueprint = array(
	array(
		array(
			"name" => "prefix",
			"caption" => "prefix",
			"placeholder" => "Prefix",
			"flex" => 2
		),
		array(
			"name" => "start",
			"caption" => "เริ่มต้น",
			"placeholder" => "From",
			"flex" => 2
		),
		array(
			"name" => "end",
			"caption" => "จำนวน",
			"placeholder" => "จำนวนถุง",
			"flex" => 2
		)
	),
	array(
		array(
			"type" => "custom",
			"caption" => "ชื่อถุง",
			"html" => $select_packtype,
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "pack_type",
			"caption" => "ประเภทถุง",
			"source" => array("ถุงปกติ", "แท่ง", "ถุงรวมเศษ", "SILVER PLATE", "SILVER ARTICLE"),
			"flex" => 4

		),
		array(
			"name" => "weight_expected",
			"caption" => "นำหนัก",
			"value" => "1",
			"flex" => 4
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
