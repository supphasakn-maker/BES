<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;

		$form = new iform($this->dbc, $this->auth);
		$form->setFrom("form_uploader");
		$form->SetVariable(array(
			array("id", $this->param['id'])
		));
		$form->SetBlueprint(array(
			array(
				array(
					"type" => "textarea",
					"name" => "title",
					"value" => $this->param['title']
				)
			)
		));
		$form->EchoInterface();
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_file_upload", "แก้ไขรายละเอียด");
$modal->setButton(array(
	array("action", "btn-danger float-sm-left", "Remove", "fn.app.production.produce.clear()"),
	array("close", "btn-secondary", "Dismiss"),
	array("action", "btn-warning float-sm-left", "Change", "fn.app.production.produce.save()")
));
$modal->EchoInterface();
$dbc->Close();
