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

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_cash","Add Cash");
	$modal->initiForm("form_addcash");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.sigmargin.cash.add()")
	));

	$blueprint = array(
		array(
			array(
				"type" => "number",
				"name" => "amount",
				"caption" => "Cash",
				"placeholder" => "Cash"
			)
		),
		array(
			array(
				"type" => "date",
				"name" => "date",
				"caption" => "Date",
				"value" => date("Y-m-d"),
				"flex" => 4
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
