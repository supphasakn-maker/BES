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
	$interest = $dbc->GetRecord("bs_smg_interest","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_interest","Edit Interest");
	$modal->initiForm("form_editinterest");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.sigmargin.interest.edit()")
	));
	$modal->SetVariable(array(
		array("id",$interest['id'])
	));

	$blueprint = array(
		array(
			array(
				"type" => "date",
				"name" => "date_start",
				"caption" => "Date Form",
				"value" => date("Y-m-d"),
				"flex" => 4,
				"value" => $interest['date_start']
			),
			array(
				"type" => "date",
				"name" => "date_end",
				"caption" => "Date to",
				"value" => date("Y-m-d"),
				"flex" => 4,
				"value" => $interest['date_end']
			)
			),
		array(
			array(
				"type" => "number",
				"name" => "interest",
				"caption" => "interest",
				"placeholder" => "interest",
				"value" => $interest['interest']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
