<?php
session_start();
include_once "../../../config/define.php";
@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../include/const.php";
include_once "../../../include/session.php";

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);
$match = $dbc->GetRecord("bs_match_data", "*", "id=" . $_POST['id']);
$json_data = json_decode($match['data'], true);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_match", "Edit Match");
$modal->initiForm("form_editmatch");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.audit.match.edit()")
));
$modal->SetVariable(array(
	array("id", $match['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"placeholder" => "Date",
			"value" => "",
			"value" => $match['date']
		)
	)
);

foreach ($aData as $field) {
	array_push($blueprint, array(
		array(
			"name" => $field[0],
			"caption" => $field[1],
			"placeholder" => $field[1],
			"flex-label" => 4,
			"flex" => 8,
			"value" => $json_data[$field[0]]
		)
	));
}

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
