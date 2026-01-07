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
	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_order","Edit Order");
	$modal->initiForm("form_editorder");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sales.order.edit()")
	));
	$modal->SetVariable(array(
		array("id",$order['id'])
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
				"value" => $order['customer_id']
			)
		),array(
			array(
				"type" => "datetime",
				"name" => "date",
				"caption" => "Date",
				"flex" => 4,
				"value" => $order['date']
			),array(
				"name" => "sales",
				"caption" => "Sales",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_employees",
					"value" => "id",
					"name" => "fullname"
				),
				"flex" => 4,
				"value" => $order['sales']
			)
		),array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "Amount",
				"placeholder" => "Amount",
				"flex"=> 4,
				"value" => $order['amount']
			),
			array(
				"type" => "number",
				"name" => "price",
				"placeholder" => "Price",
				"flex"=> 4,
				"value" => $order['price']
			),
			array(
				"name" => "currency",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_currencies",
					"value" => "code",
					"name" => "code"
				),
				"flex"=> 2,
				"value" => $order['currency']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "vat_type",
				"caption" => "VAT",
				"source" => array(
					array(0,"No VAT"),
					array(2,"7% VAT")
				),
				"value" => $order['vat_type']
			)
		),array(
			array(
				"caption" => "Delivery",
				"type" => "checkbox",
				"name" => "delivery_lock",
				"text" => "Lock",
				"class" => "pt-2",
				"flex" => 2,
				"value" => is_null($order['delivery_date'])?true:false
			),
			array(
				"type" => "date",
				"name" => "delivery_date",
				"flex" => 4,
				"value" => date("Y-m-d"),
				"value" => $order['delivery_date']
			),
			array(
				"type" => "comboboxdatabank",
				"source" => "db_time",
				"name" => "delivery_time",
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
				"flex" => 4,
				"value" => $order['delivery_time']
			)
		),array(
			array(
				"name" => "contact",
				"caption" => "Contact",
				"value" => $order['info_contact']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "comment",
				"caption" => "Comment",
				"value" => $order['comment']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "shipping_address",
				"caption" => "Shipping",
				"value" => $order['shipping_address']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "billing_address",
				"caption" => "Billing",
				"value" => $order['billing_address']
			)
		),array(
			array(
				"type" => "comboboxdatabank",
				"source" => "db_payment",
				"name" => "payment",
				"caption" => "การจ่ายเงิน",
				"flex" => 3,
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
				"value" => $order['info_payment']
			),array(
				"name" => "rate_spot",
				"caption" => "Rate",
				"placeholder" => "Spot Rate",
				"flex-label" => 1,
				"flex" => 3,
				"help" => "Spot Rate",
				"value" => $order['rate_spot']
			),array(
				"name" => "rate_exchange",
				"placeholder" => "Exchange Rate",
				"flex" => 3,
				"help" => "Exchange Reate",
				"value" => $order['rate_exchange']
			)
		),array(
			array(
				"name" => "product_id",
				"caption" => "Product Item",
				"type" => "comboboxdb",
				"source" => array(
					"table" => "bs_products",
					"value" => "id",
					"name" => "name"
				),
				"value" => $order['product_id']
			)
		),
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
