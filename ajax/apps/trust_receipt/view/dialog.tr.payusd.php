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
	
	$transfer = $dbc->GetRecord("bs_transfers","*","id=".$usd['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_payment","Payment");
	$modal->initiForm("form_payment");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.trust_receipt.tr.payment()")
	));
	$modal->SetVariable(array(
		array("purchase_id",$usd['id']),
		array("transfer_id",$transfer['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"value" => date("Y-m-d")
			)
		),array(
			array(
				"name" => "unpaid",
				"caption" => "Unpaid",
				"value" => $usd['unpaid'],
				"readonly",
				"readonly"=> true
			)
		),array(
			array(
				"name" => "rate_interest",
				"caption" => "Interest Rate",
				"value" => 0,
				"flex" => 4
			),array(
				"name" => "interest_day",
				"caption" => "Day",
				"value" => 0,
				"flex" => 4
			)
		),array(
			array(
				"name" => "interest",
				"caption" => "Interest",
				"value" => 0,
				"readonly"=> true
			)
		),array(
			array(
				"name" => "paid_thb",
				"caption" => "THB Paid",
				"value" => 0
			)
		),array(
			array(
				"name" => "remain",
				"caption" => "Remain",
				"value" => 0,
				"readonly"=> true
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
