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
	$quick_order = $dbc->GetRecord("bs_quick_orders","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_quick_order","Edit Quick_order");
	$modal->initiForm("form_editquick_order");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sales.quick_order.edit()")
	));
	$modal->SetVariable(array(
		array("id",$quick_order['id'])
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
				),
				"value" => $quick_order['customer_id']
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
				),
				"value" => $quick_order['product_id']
			)
		),array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "Amount",
				"placeholder" => "Amount",
				"flex"=> 4,
				"value" => $quick_order['amount']
			),
			array(
				"type" => "number",
				"name" => "price",
				"placeholder" => "Price",
				"flex"=> 4,
				"value" => $quick_order['price']
			),
			array(
				"type" => "combobox",
				"name" => "vat_type",
				"source" => array(
					array(0,"No VAT"),
					array(2,"7% VAT")
				),
				"flex"=> 2,
				"value" => $quick_order['vat_type']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "remark",
				"caption" => "Remark",
				"value" => $quick_order['remark']
			)
		),array(
			array(
				"name" => "rate_spot",
				"caption" => "Spot Rate",
				"placeholder" => "Spot Rate",
				"flex" => 3,
				"value" => $quick_order['rate_spot']
			),array(
				"name" => "rate_exchange",
				"caption" => "Exchange Rate",
				"placeholder" => "Exchange Rate",
				"flex" => 3,
				"value" => $quick_order['rate_exchange']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
