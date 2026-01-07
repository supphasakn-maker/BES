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
$claim = $dbc->GetRecord("bs_smg_claim", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_claim", "Edit Claim");
$modal->initiForm("form_editclaim");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin.claim.edit()")
));
$modal->SetVariable(array(
	array("id", $claim['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "amount",
			"caption" => "กิโลซื้อ/ขาย",
			"placeholder" => "กิโลซื้อ/ขาย",
			"type" => "number",
			"value" => $claim['amount']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "purchase_type",
			"source" => array(
				array("Buy", "value" => "Buy", 'Buy'),
				array("Sell", "value" => "Sell", 'Sell')
			),
			"caption" => "Buy/Sell",
			"value" => $claim['purchase_type']
		)
	),
	array(
		array(
			"name" => "rate_spot",
			"caption" => "SPOT",
			"placeholder" => "SPOT",
			"type" => "number",
			"value" => $claim['rate_spot']
		)
	),
	array(
		array(
			"name" => "rate_pmdc",
			"caption" => "Pm/Dc",
			"placeholder" => "Pm/Dc",
			"type" => "number",
			"value" => $claim['rate_pmdc']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d"),
			"flex" => 4,
			"value" => $claim['date']
		)
	)

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
