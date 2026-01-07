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
	$cash = $dbc->GetRecord("bs_smg_cash","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_cash","Edit Cash");
	$modal->initiForm("form_editcash");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sigmargin.cash.edit()")
	));
	$modal->SetVariable(array(
		array("id",$cash['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "Cash",
				"placeholder" => "Cash",
				"value" => $cash['amount']
			)
		),
		array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"value" => date("Y-m-d"),
				"flex" => 4,
				"value" => $cash['date']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
