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
$silver = $dbc->GetRecord("bs_smg_trade", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_silver", "Edit Silver");
$modal->initiForm("form_editsilver");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin.silver.edit()")
));
$modal->SetVariable(array(
	array("id", $silver['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"source" => array(
				array("Physical", "value" => "Physical", 'Physical'),
				array("Trade", "value" => "Trade", 'Trade')
			),
			"caption" => "Physical/Trade",
			"value" => $silver['type']
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
			"value" => $silver['purchase_type']
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "amount",
			"type" => "number",
			"value" => $silver['amount']
		)
	),
	array(
		array(
			"name" => "rate_spot",
			"caption" => "SPOT",
			"placeholder" => "SPOT",
			"type" => "number",
			"value" => $silver['rate_spot']
		)
	),
	array(
		array(
			"name" => "rate_pmdc",
			"caption" => "Pm/Dc",
			"placeholder" => "Pm/Dc",
			"type" => "number",
			"value" => $silver['rate_pmdc']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => $silver['date'],
			"flex" => 4
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
