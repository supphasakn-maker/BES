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

	$modal->setModel("dialog_pay_payment","Payment");
	$modal->initiForm("form_paypayment");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.prepare_to_pay.payment.pay()")
	));
	$modal->SetVariable(array(
		array("id",$order['id']),
		array("customer_id",$order['customer_id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "วันเวลา",
				"flex" => 3,
				"value" => date("Y-m-d")
			),array(
				"type" => "time",
				"name" => "time",
				"flex" => 2,
				"value" => date("H:i")
			),array(
				"type" => "comboboxdatabank",
				"caption" => "การชำระเงิน",
				"source" => "db_payment",
				"name" => "payment",

				"flex" => 3
			)
		),array(
			array(
				"type" => "date",
				"name" => "date_active",
				"caption" => "วันเงินเข้า",
				"flex" => 3
			)
		),array(
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
			),array(
				"placeholder" => "หมายเลขอ้างอิง",
				"name" => "ref",
				"flex" => 6
			)
		),array(
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
		),array(
			array(
				"caption" => "จำนวนเงิน",
				"name" => "amount",
				"value" => $order['net']
			)
		)
	);
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
