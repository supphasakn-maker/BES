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
$adjust = $dbc->GetRecord("bs_adjust_amount", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_adjust", "EDIT AMOUNT");
$modal->initiForm("form_editadjust");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.defer_adjust.adjust.edit()")
));
$modal->SetVariable(array(
	array("id", $adjust['id'])
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
			"value" => $adjust['supplier_id']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"flex" => 4,
			"value" => $adjust['date']
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "AMOUNT",
			"placeholder" => "Total amount in THB",
			"value" => $adjust['amount']
		)
	),
	array(
		array(
			"name" => "usd",
			"caption" => "USD",
			"placeholder" => "Total amount in USD",
			"value" => $adjust['usd']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
