<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_prepare", "Add Prepare");
$modal->initiForm("form_addprepare");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.production_silverplate.prepare.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "round_id",
			"type" => "comboboxdb",
			"caption" => "Round Number",
			"source" => array(
				"table" => "bs_productions_round",
				"value" => "id",
				"name" => "import_lot",
				"where" => "status = 1"

			)
		)
	),
	array(
		array(
			"name" => "import_lot",
			"caption" => "Round Number",
			"placeholder" => "Round Number"
		),
	),
	array(
		array(
			"name" => "amount",
			"caption" => "จำนวนสั่งผลิต",
			"placeholder" => "amount"
		),
	),
	array(
		array(
			"name" => "amount_balance",
			"caption" => "จำนวนที่นำเข้า",
			"placeholder" => "amount",
			"readonly" => "readonly"
		),
	),
	array(
		array(
			"name" => "balance",
			"caption" => "จำนวนคงเหลือ",
			"placeholder" => "amount",
			"readonly" => "readonly"
		),
	),
	array(
		array(
			"name" => "product_type_id",
			"caption" => "สินค้า",
			"placeholder" => "สินค้า",
			"readonly" => "readonly"
		),
	),
	array(
		array(
			"name" => "PMR",
			"caption" => "การผลิต",
			"placeholder" => "การผลิต",
			"readonly" => "readonly"
		),
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
