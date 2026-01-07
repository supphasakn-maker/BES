<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$statement = $dbc->GetRecord("bs_coa_run", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_run-number", "Edit");
$modal->initiForm("form_editrun-number");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.coa_coc.run_number.edit()")
));
$modal->SetVariable(array(
	array("id", $statement['id'])
));


$blueprint = array(
	array(
		array(
			"name" => "number",
			"caption" => "COA",
			"placeholder" => "COA",
			"readonly" => "readonly",
			"value" => $statement['number']
		)
	),
	array(
		array(
			"name" => "number_coc",
			"caption" => "COC",
			"placeholder" => "COC",
			"readonly" => "readonly",
			"value" => $statement['number_coc']
		)
	),
	array(
		array(
			"name" => "order_id",
			"type" => "comboboxdb",
			"caption" => "หมายเลข Order",
			"source" => array(
				"table" => "bs_orders",
				"name" => "code",
				"value" => "id",
				"where" => "DATE(date) > '2024-12-01'"
			)
		)
	),
	array(
		array(
			"name" => "customer_name",
			"readonly" => "readonly",
			"caption" => "ลูกค้า",
		)
	),
	array(
		array(
			"name" => "customer_id",
			"readonly" => "readonly",
			"caption" => "รหัส ลูกค้า",
		)
	),
	array(
		array(
			"name" => "order_code",
			"readonly" => "readonly",
			"caption" => "หมายเลข Order",
		)
	),
	array(
		array(
			"name" => "delivery_date",
			"readonly" => "readonly",
			"caption" => "วันที่ส่งของ",
			"value" => date("Y-m-d"),
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
