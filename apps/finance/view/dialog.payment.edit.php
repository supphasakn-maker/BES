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
$payment = $dbc->GetRecord("bs_payments", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_payment", "Edit Payment");
$modal->initiForm("form_editpayment");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.finance.payment.edit()")
));
$modal->SetVariable(array(
	array("id", $payment['id'])
));


$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "วันเวลา",
			"flex" => 3,
			"value" => date("Y-m-d", strtotime($payment['datetime']))
		),
		array(
			"type" => "time",
			"name" => "time",
			"flex" => 2,
			"value" => date("H:i", strtotime($payment['datetime']))
		),
		array(
			"type" => "comboboxdatabank",
			"caption" => "การชำระเงิน",
			"source" => "db_payment",
			"name" => "payment",
			"value" => $payment['payment'],
			"flex" => 3
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date_active",
			"caption" => "วันเวลาเงินเข้า",
			"value" => $payment['date_active'],
			"flex" => 3
		)
	),
	array(
		array(
			"name" => "customer_id",
			"caption" => "ลูกค้า",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_customers",
				"value" => "id",
				"name" => "name"
			),
			"value" => $payment['customer_id']
		)
	),
	array(
		array(
			"type" => "comboboxdatabank",
			"caption" => "จากธนาคาร",
			"source" => "db_bank",
			"name" => "customer_bank",
			"flex" => 4,
			"default" => array(
				"value" => "none",
				"name" => "ไม่ระบุ"
			),
			"value" => $payment['customer_bank']
		),
		array(
			"placeholder" => "หมายเลขอ้างอิง",
			"name" => "ref",
			"flex" => 6,
			"value" => $payment['ref']
		)
	),
	array(
		array(
			"name" => "bank_id",
			"caption" => "เข้าบัญชี",
			"type" => "comboboxdb",
			"source" => array(
				"table" => "bs_banks",
				"value" => "id",
				"name" => "name"
			),
			"default" => array(
				"value" => "NULL",
				"name" => "ไม่ระบุ"
			),
			"value" => $payment['bank_id']
		)
	),
	array(
		array(
			"caption" => "จำนวนเงิน",
			"name" => "amount",
			"value" => $payment['amount']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
