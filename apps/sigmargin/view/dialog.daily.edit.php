<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";


$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$daily = $dbc->GetRecord("bs_smg_daily", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_daily", "Edit Daily");
$modal->initiForm("form_editdaily");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin.daily.edit()")
));
$modal->SetVariable(array(
	array("id", $daily['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => $daily['date']
		)
	),
	array(
		array(
			"name" => "rollover",
			"caption" => "Rollover",
			"placeholder" => "Rollover",
			"value" => $daily['rollover']
		)
	),
	array(
		array(
			"name" => "spot_sell",
			"caption" => "Spot Sell",
			"placeholder" => "Spot Sell",
			"value" => $daily['spot_sell']
		)
	),
	array(
		array(
			"name" => "spot_buy",
			"caption" => "Spot Buy",
			"placeholder" => "Spot Buy",
			"value" => $daily['spot_buy']
		)
	),
	array(
		array(
			"name" => "cash",
			"caption" => "Cash",
			"placeholder" => "Cash",
			"value" => $daily['cash']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
