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
	$rollover = $dbc->GetRecord("bs_smg_rollover","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_rollover","Edit Rollover");
	$modal->initiForm("form_editrollover");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sigmargin.rollover.edit()")
	));
	$modal->SetVariable(array(
		array("id",$rollover['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "combobox",
				"name" => "type",
				"source" => array(
					array("Buy" , "value" => "Buy", 'Buy'),
					array("Sell", "value" => "Sell" ,'Sell')
				),
				"caption" => "Buy/Sell",
				"value" => $rollover['type']
			)
		),
		array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "KGS",
				"placeholder" => "KGS",
				"value" => $rollover['amount']
			)
		),
		array(
			array(
				"type" => "number",
				"name" => "rate_spot",
				"caption" => "SPOT",
				"placeholder" => "SPOT",
				"value" => $rollover['rate_spot']
			)
		),
		array(
			array(
				"type" => "date",
				"name" => "trade",
				"caption" => "trade Date",
				"value" => date("Y-m-d"),
				"flex" => 4,
				"value" => $rollover['trade']
			),
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Value Date",
				"value" => date("Y-m-d"),
				"flex" => 4,
				"value" => $rollover['date']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
