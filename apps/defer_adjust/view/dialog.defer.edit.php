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
$defer = $dbc->GetRecord("bs_adjust_defer", "*", "id=" . $_POST['id']);


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_defer", "EDIT ADJUST DEFER");
$modal->initiForm("form_editdefer");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.defer_adjust.defer.edit()")
));
$modal->SetVariable(array(
	array("id", $defer['id'])
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
			"value" => $defer['supplier_id']
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"flex" => 4,
			"value" => $defer['date']
		)
	),
	array(
		array(
			"name" => "value_adjust_type",
			"caption" => "ADJUST",
			"placeholder" => "Total adjust in THB",
			"value" => $defer['value_adjust_type']
		)
	),
	array(
		array(
			"name" => "product_id",
			"caption" => "PRODUCT",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_products",
				"value" => "id",
				"name" => "name"
			),
			"value" => $defer['product_id']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
