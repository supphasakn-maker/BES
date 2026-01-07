<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);

	class myModel extends imodal{
		function body(){
			$dbc = $this->dbc;
			$ohter = $dbc->GetRecord("ohters","*","id=".$this->param['id']);
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_apporve_ohter","Apporve Ohter");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Apporve","fn.app.sigmargin.ohter.apporve()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
