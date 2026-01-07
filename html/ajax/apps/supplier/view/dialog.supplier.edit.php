<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);
	$supplier = $dbc->GetRecord("bs_suppliers","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_supplier","Edit Supplier");
	$modal->initiForm("form_editsupplier");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-default","Save Change","fn.app.supplier.supplier.edit()")
	));
	$modal->SetVariable(array(
		array("id",$supplier['id'])
	));
	
	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Supplier Name",
				"value" => $supplier['name']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "comment",
				"caption" => "Comment",
				"placeholder" => "Supplier Comment",
				"value" => $supplier['comment']
			)
		),array(
			array(
				"type" => "comboboxdb",
				"name" => "gid",
				"caption" => "Group",
				"source" => array(
					"table" => "bs_supplier_groups",
					"name" => "name",
					"value" => "id"
				),
				"value" => $supplier['gid']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "type",
				"caption" => "Type",
				"source" => array(
					array(1,"Purchase with USD"),
					array(2,"THB")
				),
				"value" => $supplier['type']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
