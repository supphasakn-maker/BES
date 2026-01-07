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
$import = $dbc->GetRecord("bs_imports", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);

$modal->setModel("dialog_edit_import", "Edit Import");
$modal->initiForm("form_editimport");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-outline-dark", "Save Change", "fn.app.import.import.edit()")
));
$modal->SetVariable(array(
	array("id", $import['id'])
));

$blueprint = array(
	array(
		array(
			"type" => "date",
			"name" => "delivery_date",
			"caption" => "Delviery Date",
			"placeholder" => "Delviery Date",
			"value" => $import['delivery_date']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "delivery_by",
			"source" => array(
				"Brink",
				"G4S",
				"รับเอง"
			),
			"caption" => "Delviery By",
			"value" => $import['delivery_by']
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"caption" => "Type",
			"source" => array(
				"แท่ง",
				"เม็ด"
			),
			"value" => $import['type']
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Remark",
			"placeholder" => "Remark",
			"value" => $import['comment']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
