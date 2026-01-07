<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_quick_order","Add Quick_order");
	$modal->initiForm("form_addquick_order");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.sales.quick_order.add()")
	));
	
	$rate_exchange = $os->load_variable("rate_exchange");
	$rate_spot = $os->load_variable("rate_spot");
	$rate_pmdc = $os->load_variable("rate_pmdc");

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
		),array(
			array(
				"name" => "product_id",
				"caption" => "Product",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_products",
					"value" => "id",
					"name" => "name"
				)
			)
		),array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "Amount",
				"placeholder" => "Amount",
				"flex"=> 4
			),
			array(
				"type" => "number",
				"name" => "price",
				"placeholder" => "Price",
				"flex"=> 4
			),
			array(
				"type" => "combobox",
				"name" => "vat_type",
				"source" => array(
					array(0,"No VAT"),
					array(2,"7% VAT")
				),
				"flex"=> 2
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "remark",
				"caption" => "Remark"
			)
		),array(
			array(
				"name" => "rate_spot",
				"caption" => "Spot Rate",
				"placeholder" => "Spot Rate",
				"flex" => 3,
				"value" => $rate_spot
			),array(
				"name" => "rate_exchange",
				"caption" => "Exchange Rate",
				"placeholder" => "Exchange Rate",
				"flex" => 3,
				"value" => $rate_exchange
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
