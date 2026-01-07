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
$purchase = $dbc->GetRecord("bs_adjust_purchase", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_purchase", "EDIT PURCHASE");
$modal->initiForm("form_editpurchase");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.defer_adjust.purchase.edit()")
));
$modal->SetVariable(array(
	array("id", $purchase['id'])
));



$blueprint = array(
	array(
		array(
			"name" => "supplier_id",
			"caption" => "Supplier",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_suppliers",
				"value" => "id",
				"name" => "name",
				"where" => "status = 1"
			),
			"value" => $purchase['supplier_id']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"flex" => 4,
			"value" => $purchase['date']
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "AMOUNT",
			"placeholder" => "Total amount in KG",
			"value" => $purchase['amount']
		)
	),
	array(
		array(
			"name" => "usd",
			"caption" => "USD",
			"placeholder" => "Total amount in USD",
			"value" => $purchase['usd']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
