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
$modal->setModel("dialog_add_payment", "Add Payment");
$modal->initiForm("form_addpayment");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.finance.payment.add()")
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "วันเวลา",
			"flex" => 3
		),
		array(
			"type" => "time",
			"name" => "time",
			"value" => date("H:i:s"),
			"flex" => 2
		),
		array(
			"type" => "comboboxdatabank",
			"caption" => "การชำระเงิน",
			"source" => "db_payment",
			"name" => "payment",

			"flex" => 3
		)
	),
	array(
		array(
			"type" => "date",
			"name" => "date_active",
			"caption" => "วันเวลาเงินเข้า",
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
			)
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
			)
		),
		array(
			"placeholder" => "หมายเลขอ้างอิง",
			"name" => "ref",
			"flex" => 6
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
			)
		)
	),
	array(
		array(
			"caption" => "จำนวนเงิน",
			"name" => "amount",
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
