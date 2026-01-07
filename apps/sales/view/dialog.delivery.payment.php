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
$delivery = $dbc->GetRecord("bs_deliveries", "*", "id=" . $_POST['id']);
$order = $dbc->GetRecord("bs_orders", "*", "delivery_id=" . $delivery['id']);
$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $order['customer_id']);


if ($delivery['payment_note'] == null) {
	$payment_note = array(
		"bank" => $customer['default_bank'],
		"payment" => $customer['default_payment'],
		"remark" => ""
	);
} else {
	$payment_note = json_decode($delivery['payment_note'], true);
}


$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_payment_delivery", "Payment");
$modal->initiForm("form_paymentdelivery");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sales.delivery.payment()")
));
$modal->SetVariable(array(
	array("id", $delivery['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "comboboxdatabank",
			"source" => "db_bank",
			"name" => "bank",
			"caption" => "ธนาคาร",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex" => 4,
			"flex-label" => 1,
			"placeholder" => "Bank Detail",
			"value" => $payment_note['bank']
		),
		array(
			"type" => "comboboxdatabank",
			"source" => "db_payment",
			"name" => "payment",
			"caption" => "เงือนไขการชำระเงิน",
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"flex-label" => 3,
			"flex" => 4,
			"value" => $payment_note['payment']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "remark",
			"caption" => "ข้อมูลเพิ่มเติม",
			"value" => $payment_note['remark']
		)

	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
