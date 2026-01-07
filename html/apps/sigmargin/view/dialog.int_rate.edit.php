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
$int_rate = $dbc->GetRecord("bs_smg_rate", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_int_rate", "Edit Int_rate");
$modal->initiForm("form_editint_rate");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.sigmargin.int_rate.edit()")
));
$modal->SetVariable(array(
	array("id", $int_rate['id'])
));

$blueprint = array(

	array(
		array(
			"type" => "date",
			"name" => "date",
			"caption" => "Date",
			"value" => $int_rate['date']
		)
	),
	array(
		array(
			"name" => "rate_short",
			"caption" => "Rate (-) Short",
			"placeholder" => "Rate (-) Short",
			"value" => $int_rate['rate_short']
		)
	),
	array(
		array(
			"name" => "rate",
			"caption" => "Rate (+) Long",
			"placeholder" => "Rate (+) Long",
			"value" => $int_rate['rate']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
