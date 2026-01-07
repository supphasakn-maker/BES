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
	$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_usd","Edit USD #".$_POST['id']);
	$modal->initiForm("form_editusd");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.purchase.usd.edit()")
	));
	$modal->SetVariable(array(
		array("id",$usd['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "comboboxdatabank",
				"source" => "db_bank",
				"name" => "bank",
				"caption" => "Bank",
				"value" => $usd['bank']
			)
		),array(
			array(
				"name" => "type",
				"type" => "combobox",
				"caption" => "Type",
				"source" => array(
					array("physical","Physical"),
					array("stock","Stock"),
					array("trade","Trade")
				),
				"value" => $usd['type']
			)
		),array(
			array(
				"name" => "amount",
				"caption" => "Amount",
				"flex" => 4,
				"placeholder" => "Amount To Purchase",
				"value" => $usd['amount']
				
			),array(
				"name" => "rate_exchange",
				"caption" => "Exchange",
				"placeholder" => "Exchange Rate",
				"flex" => 4,
				"value" => $usd['rate_exchange']
			)
		),array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"placeholder" => "Purchase Date",
				"value" => $usd['date']
			)
		),array(
			array(
				"name" => "method",
				"type" => "combobox",
				"caption" => "Method",
				"flex" => 2,
				"value" => $usd['method'],
				"source" => array(
					"Today",
					"Forward"
				)
			),array(
				"name" => "ref",
				"caption" => "Deal ID",
				"flex" => 6,
				"placeholder" => "Reference",
				"value" => $usd['ref']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "comment",
				"caption" => "Comment",
				"placeholder" => "Comment",
				"value" => $usd['comment']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
