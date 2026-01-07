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

	$modal->setModel("dialog_purchase_usd","Purchase USD");
	$modal->initiForm("form_purchaseusd");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.purchase.usd.purchase()")
	));
	$modal->SetVariable(array(
		array("id",$usd['id'])
	));

	
	$blueprint = array(
		array(
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
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"placeholder" => "Purchase Date",
				"value" => $usd['date']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
