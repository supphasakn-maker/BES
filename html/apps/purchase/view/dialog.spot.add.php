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

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_spot", "Add Spot");
$modal->initiForm("form_addspot");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.purchase.spot.add()")
));

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

$blueprint = array(
	array(
		array(
			"name" => "supplier_id",
			"caption" => "Supplier",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_suppliers",
				"value" => "id",
				"name" => "name"
			)
		)
	),
	array(
		array(
			"name" => "type",
			"type" => "combobox",
			"caption" => "Type",
			"source" => array(
				array("physical", "Physical"),
				array("stock", "Stock"),
				array("trade", "Trade"),
				array("defer", "Defer"),
				array("physical-adjust", "Physical-Adjust")
			)
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount To Purchase",
			"flex" => 6
		),
		array(
			"name" => "currency",
			"caption" => "Currency",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_currencies",
				"value" => "code",
				"name" => "code"
			)
		)
	),
	array(
		array(
			"name" => "THBValue",
			"caption" => "THBValue",
			"placeholder" => "Total Purchase in THB",
			"value" => $spot['THBValue']
		)
	),
	array(
		array(
			"name" => "rate_spot",
			"caption" => "Spot",
			"placeholder" => "Spot Name",
			"flex" => 4,
			"value" => $rate_spot
		),
		array(
			"name" => "rate_pmdc",
			"caption" => "Pm/Dc",
			"placeholder" => "premium/discount",
			"flex" => 4,
			"value" => $rate_pmdc
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Purchase Date",
			"value" => date("Y-m-d")
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "value_date",
			"caption" => "Value Date",
			"placeholder" => "Value Date",
			"value" => date("Y-m-d")
		)
	),
	array(
		array(
			"name" => "method",
			"type" => "combobox",
			"caption" => "Method",
			"flex" => 2,
			"source" => array(
				"Call To Buy",
				"Deal ID",
				"Via Message"
			)
		),
		array(
			"name" => "ref",
			"caption" => "Reference",
			"flex" => 6,
			"placeholder" => "Reference"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment",
			"placeholder" => "Comment"
		)

	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
