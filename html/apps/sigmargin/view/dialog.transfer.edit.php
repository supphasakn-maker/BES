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
$transfer = $dbc->GetRecord("bs_smg_payment", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_transfer", "Edit Transfer");
$modal->initiForm("form_edittransfer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin.transfer.edit()")
));
$modal->SetVariable(array(
	array("id", $transfer['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"source" => array(
				array("ค่าสินค้า", "value" => "ค่าสินค้า", 'ค่าสินค้า'),
				array("โอนมัดจำ", "value" => "โอนมัดจำ", 'โอนมัดจำ')
			),
			"caption" => "ค่าสินค้า/โอนมัดจำ",
			"value" => $transfer['type']
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount_usd",
			"placeholder" => "USD",
			"caption" => "USD",
			"value" => $transfer['amount_usd']
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "rate_pmdc",
			"placeholder" => "Pm / Dc",
			"caption" => "Pm / Dc",
			"value" => $transfer['rate_pmdc']
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount_total",
			"placeholder" => "Total",
			"caption" => "Total",
			"value" => $transfer['amount_total']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d"),
			"flex" => 4,
			"value" => $transfer['date']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
