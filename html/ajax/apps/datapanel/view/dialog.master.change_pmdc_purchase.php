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
	$rate_pmdc = $os->load_variable("rate_pmdc_purchase");
	
	
	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_change_pmdc_master_purchase","Change Premium/Discount Rate");
	$modal->initiForm("form_change_pmdcmaster");
	
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Change","fn.app.datapanel.master.change_pmdc_purchase()")
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "rate",
				"caption" => "Rate",
				"placeholder" => "Premium/Discount Rate",
				"value" => $rate_pmdc
			)
		)
	);
	
	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();

	$dbc->Close();
?>