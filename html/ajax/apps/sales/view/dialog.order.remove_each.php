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
	$order = $dbc->GetRecord("bs_orders","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_remove_each_order","Remove Order");
	$modal->initiForm("form_remove_eachorder");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Remove","fn.app.sales.order.remove_each()")
	));
	$modal->SetVariable(array(
		array("id",$order['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "remove_reason",
				"caption" => "Reason",
				"type" => "textarea"
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
