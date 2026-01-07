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

	$modal = new imodal($dbc,$os->auth);
	$modal->setModel("dialog_add_department","Add Department");
	$modal->initiForm("form_adddepartment");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-primary","Save Change","fn.app.employee.department.add()")
	));

	$blueprint = array(
		array(
			array(
				"name" => "name",
				"caption" => "Name",
				"placeholder" => "Department Name"
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();

?>
