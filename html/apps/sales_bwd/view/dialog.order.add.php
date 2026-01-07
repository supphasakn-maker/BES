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
$modal->setModel("dialog_add_order", "Add Order");
$modal->initiForm("form_addorder");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.sales_bwd.order.add()")
));



$blueprint = array(
	array(
		array(
			"name" => "customer_id",
			"caption" => "Customer",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_customers",
				"value" => "id",
				"name" => "name"
			)
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => date("Y-m-d"),
			"flex" => 4
		),
		array(
			"name" => "sales",
			"caption" => "Sales",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_employees",
				"value" => "id",
				"name" => "fullname"
			),
			"flex" => 4
		)
	),
	array(
		array(
			"type" => "number",
			"name" => "amount",
			"caption" => "Amount",
			"placeholder" => "Amount",
			"flex" => 4
		),
		array(
			"type" => "number",
			"name" => "price",
			"placeholder" => "Price",
			"flex" => 4
		),
		array(
			"name" => "currency",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_currencies",
				"value" => "code",
				"name" => "code"
			),
			"flex" => 2
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "vat_type",
			"caption" => "VAT",
			"source" => array(
				array(0, "No VAT"),
				array(2, "7% VAT")
			)
		)
	),
	array(
		array(
			"caption" => "Delivery",
			"type" => "checkbox",
			"name" => "delivery_lock",
			"text" => "Lock",
			"class" => "pt-2",
			"flex" => 2,
		),
		array(
			"type" => "date",
			"name" => "delivery_date",
			"flex" => 4,
			"value" => date("Y-m-d")
		),
		array(
			"type" => "comboboxdatabank",
			"source" => "db_time",
			"name" => "delivery_time",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex" => 4
		)
	),
	array(
		array(
			"name" => "contact",
			"caption" => "Contact"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "shipping_address",
			"caption" => "Shipping"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "billing_address",
			"caption" => "Billing"
		)
	),
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_payment",
			"name" => "payment",
			"caption" => "การจ่ายเงิน",
			"flex" => 3,
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			)
		),
		array(
			"name" => "rate_spot",
			"caption" => "Rate",
			"placeholder" => "Spot Rate",
			"flex-label" => 1,
			"flex" => 3,
			"value" => $rate_spot,
			"help" => "Spot Rate"
		),
		array(
			"name" => "rate_exchange",
			"placeholder" => "Exchange Rate",
			"flex" => 3,
			"value" => $rate_exchange,
			"help" => "Exchange Reate"
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
