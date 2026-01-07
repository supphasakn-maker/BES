<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);
	$rate_exchange = $os->load_variable("rate_exchange");
	
	
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_change_exchange_master","Change Exchange Rate");
	$modal->initiForm("form_change_exchangemaster");
	
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Change","fn.app.datapanel.master.change_exchange()")
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "rate",
				"caption" => "Rate",
				"placeholder" => "Exchagne Rate",
				"value" => $rate_exchange
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();

	$dbc->Close();
?>
