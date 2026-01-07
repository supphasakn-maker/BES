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
$trading = $dbc->GetRecord("bs_trade_spot", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_trading", "Edit Trading");
$modal->initiForm("form_edittrading");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.trade_spot.trading.edit()")
));
$modal->SetVariable(array(
	array("id", $trading['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Date",
			"value" => $trading['date']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
