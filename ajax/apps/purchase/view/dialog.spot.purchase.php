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
	$spot = $dbc->GetRecord("bs_purchase_spot","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_purchase_spot","Purchase SPOT");
	$modal->initiForm("form_purchasespot");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.purchase.spot.purchase()")
	));
	$modal->SetVariable(array(
		array("id",$spot['id'])
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
					array("trade","Trade"),
					array("defer","Defer")
				),
				"value" => $spot['type']
			)
		),array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"placeholder" => "Purchase Date",
				"value" => $spot['date']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
