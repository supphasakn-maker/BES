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
$scrap = $dbc->GetRecord("bs_scrap_items", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_editrefine_scrap", "Edit Scrap");
$modal->initiForm("form_editscrap");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.production_scrap.scrap.edit_refine()")
));
$modal->SetVariable(array(
	array("id", $scrap['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "weight_expected",
			"caption" => "เศษ",
			"placeholder" => "เศษ",
			"value" => $scrap['weight_expected']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
