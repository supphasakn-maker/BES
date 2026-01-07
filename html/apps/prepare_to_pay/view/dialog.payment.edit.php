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

	$modal->setModel("dialog_edit_payment","Edit Payment");
	$modal->initiForm("form_editpayment");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.prepare_to_pay.payment.edit()")
	));
	$modal->SetVariable(array(
		array("id",$order['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "date",
				"name" => "name",
				"caption" => "วันที่ชำระเงิน",
				"placeholder" => "Payment Name"
			)
		),array(
			
			array(
				"type" => "comboboxdatabank",
				"source" => "db_payment",
				"name" => "default_payment",
				"caption" => "แผนการเงิน",
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
				"flex" => 6
			)
		),array(
			array(
				"type" => "comboboxdatabank",
				"source" => "db_bank",
				"name" => "bank",
				"caption" => "ธนาคาร",
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
				"flex" => 6,
				"placeholder" => "Bank Detail"
			)
		),array(
			array(
				"type" => "comboboxdatabank",
				"source" => "db_payment_method",
				"name" => "default_payment",
				"caption" => "วิธีการชำระเงิน",
				"default" => array(
					"value" => "none",
					"name" => "ไม่ระบุ"
				),
			)
		),array(
			array(
				"name" => "name",
				"caption" => "ค่าขนส่ง",
				"placeholder" => "ค่าขนส่ง",
				"flex" => 6
			),array(
				"name" => "name",
				"placeholder" => "ทศพนิยม",
				"flex" => 4
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "name",
				"caption" => "การเข้าเช็ค",
				"source" => array(
					"ทัน","ไม่ทัน"
				)
			)
			
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
