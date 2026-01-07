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
$modal->setModel("dialog_add_supplier", "Add Supplier");
$modal->initiForm("form_addsupplier");
$modal->setExtraClass("modal-lg");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-primary", "Save Change", "fn.app.supplier.supplier.add()")
));

$blueprint = array(
	array(
		array(
			"name" => "name",
			"caption" => "Name",
			"placeholder" => "Supplier Name"
		)
	),
	array(
		array(
			"type" => "textarea",
			"name" => "comment",
			"caption" => "Comment",
			"placeholder" => "Supplier Comment"
		)
	),
	array(
		array(
			"type" => "comboboxdb",
			"name" => "gid",
			"caption" => "Group",
			"source" => array(
				"table" => "bs_supplier_groups",
				"name" => "name",
				"value" => "id"
			)
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "type",
			"caption" => "Type",
			"source" => array(
				array(1, "Purchase with USD"),
				array(2, "THB")
			)
		)
	),
	array(
		array(
			"type" => "combobox",
			"name" => "status",
			"caption" => "Status",
			"source" => array(
				array(1, "ENABLED"),
				array(2, "DISABLED")
			)
		)
	)
);

$modal->SetBlueprint($blueprint);
$modal->EchoInterface();
$dbc->Close();
