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
	$type = $dbc->GetRecord("bs_stock_adjuest_types","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_type","Edit Type");
	$modal->initiForm("form_edittype");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.stock.type.edit()")
	));
	$modal->SetVariable(array(
		array("id",$type['id'])
	));

	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Type Name",
				"value" => $type['name']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "type",
				"caption" => "Type",
				"source" => array(
					array(1,"Included"),
					array(2,"Memo")
				),
				"value" => $type['type']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
