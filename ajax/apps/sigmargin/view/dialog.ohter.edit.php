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
	$ohter = $dbc->GetRecord("bs_smg_other","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_ohter","Edit Ohter");
	$modal->initiForm("form_editohter");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sigmargin.ohter.edit()")
	));
	$modal->SetVariable(array(
		array("id",$ohter['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "number",
				"name" => "usd_debit",
				"caption" => "Dabit (USD)",
				"placeholder" => "Dabit (USD)",
				"value" => $ohter['usd_debit']
			)
		),
		array(
			array(
				"type" => "number",
				"name" => "usd_credit",
				"caption" => "Credit (USD)",
				"placeholder" => "Credit (USD)",
				"value" => $ohter['usd_credit']
			)
		),
		array(
			array(
				"type" => "number",
				"name" => "amount_debit",
				"caption" => "Debit (Silver)",
				"placeholder" => "Debit (Silver)",
				"value" => $ohter['amount_debit']
			)
		),
		array(
			array(
				"type" => "number",
				"name" => "amount_credit",
				"caption" => "Credit (Silver)",
				"placeholder" => "Credit (Silver)",
				"value" => $ohter['amount_credit']
			)
		),
		array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"value" => $ohter['date'],
				"flex" => 4
			)
		),
		array(
			array(
				"type" => "input",
				"name" => "remark",
				"caption" => "Remark",
				"placeholder" => "Remark",
				"value" => $ohter['remark']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
