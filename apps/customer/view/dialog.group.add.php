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

$modal = new imodal($dbc, $os->auth);
$modal->setModel("dialog_add_group", "Add Group");
$modal->initiForm("form_addgroup");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.customer.group.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Group Name"
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"name" => "product_id",
			"caption" => "Product",
			"source" => array(
				"table" => "bs_products",
				"name" => "name",
				"value" => "id"

			),
			"default" => array(
				"value" => "NULL",
				"name" => "ไม่ระบุ"
			)
		)

	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
