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
	$department = $dbc->GetRecord("bs_departments","*","id=".$_POST['id']);

	$modal = new imodal($dbc,$os->auth);

	$modal->setModel("dialog_edit_department","Edit Department");
	$modal->initiForm("form_editdepartment");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-default","Save Change","fn.app.employee.department.edit()")
	));
	$modal->SetVariable(array(
		array("id",$department['id'])
	));

	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Department Name",
				"value" => $department['name']
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
