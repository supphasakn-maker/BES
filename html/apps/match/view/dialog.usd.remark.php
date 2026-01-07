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
$usd = $dbc->GetRecord("bs_mapping_usd", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_remark_usd", "Remark USD");
$modal->initiForm("form_remarkusd");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.match.usd.remark()")
));
$modal->SetVariable(array(
	array("id", $usd['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "textarea",
			"name" => "remark",
			"caption" => "remark",
			"placeholder" => "USD Name",
			"value" => $usd['remark']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
