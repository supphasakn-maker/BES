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
$group = $dbc->GetRecord("os_groups", "*", "id=" . $_POST['id']);

$modal = new imodal($dbc, $os->auth);
//$modal->setParam($_POST);
$modal->setModel("dialog_edit_group", "Edit Group");
$modal->initiForm("form_editgroup");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.accctrl.group.edit()")
));
$modal->SetVariable(array(
	array("group_id", $group['id'])
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Group Name",
			"value" => $group['name']
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"source" => array(
				"table" => "os_accounts",
				"name" => "name",
				"value" => "id"
			),
			"default" => array(
				"name" => "No Account",
				"value" => "NULL"
			),
			"name" => "account",
			"caption" => "Account",
			"value" => $group['account']
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
