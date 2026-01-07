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
$reserve = $dbc->GetRecord("bs_reserve_silver", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_reserve", "Edit Reserve");
$modal->initiForm("form_editreserve");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.reserve_silver.reserve.edit()")
));
$modal->SetVariable(array(
	array("id", $reserve['id'])
));


$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "lock_date",
			"caption" => "Lock Date",
			"value" => $reserve['lock_date']
		)
	),
	array(
		array(
			"name" => "supplier_id",
			"caption" => "Supplier",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_suppliers",
				"value" => "id",
				"name" => "name"
			),
			"value" => $reserve['supplier_id']
		)
	),
	array(
		array(
			"name" => "weight_lock",
			"caption" => "Kilo Lock",
			"placeholder" => "Kilo to Lock",
			"value" => $reserve['weight_lock']
		)
	),
	array(
		array(
			"name" => "weight_actual",
			"caption" => "Actual Weight",
			"placeholder" => "Actual",
			"value" => $reserve['weight_actual'],
			"flex" => 6
		),
		array(
			"name" => "bar",
			"caption" => "Bar",
			"placeholder" => "Bar",
			"value" => $reserve['bar'],
			"flex" => 2
		)
	),
	array(
		array(
			"name" => "discount",
			"caption" => "Discount",
			"placeholder" => "Locked Discount",
			"value" => $reserve['discount']
		)
	),
	array(
		array(
			"name" => "type",
			"type" => "combobox",
			"caption" => "Type",
			"flex" => 2,
			"source" => array(
				array(1, "ใช้จริง"),
				array(2, "สำรอง")
			),
			"value" => $reserve['type']
		)
	),
	array(
		array(
			"name" => "brand",
			"caption" => "Brand",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_products_import",
				"value" => "code",
				"name" => "name"
			),
			"value" => $reserve['brand']
		)
	)

);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
