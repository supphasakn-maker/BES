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
$modal->setModel("dialog_add_usd", "Add Usd");
$modal->initiForm("form_addusd");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.purchase.usd.add()")
));

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

$blueprint = array(
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_bank",
			"name" => "bank",
			"caption" => "Bank",
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
				array("fee", "Fee"),
				array("Interest to STD", "Interest to STD"),
				array("Interest to STX", "Interest to STX")
			)
		)
	),
	array(
		array(
			"name" => "amount",
			"caption" => "Amount",
			"flex" => 4,
			"placeholder" => "Amount To Purchase",

		),
		array(
			"name" => "rate_exchange",
			"caption" => "Exchange",
			"placeholder" => "Exchange Rate",
			"flex" => 4,
			"value" => $rate_exchange
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
			"name" => "method",
			"type" => "combobox",
			"caption" => "Method",
			"flex" => 2,
			"source" => array(
				"Today",
				"Forward",
				"TOM",
				"SPOT",
				"1D",
				"1W",
				"1M",
				"2M",
				"3M",
			)
		),
		array(
			"name" => "ref",
			"caption" => "Deal ID",
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
