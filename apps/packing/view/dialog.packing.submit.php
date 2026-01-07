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
			$packing = $dbc->GetRecord("bs_packings","*","id=".$this->param['id']);
			echo "Are you sure to submit";
			echo '<form name="form_submitpacking" data-iterator="1">';
				echo '<input type="hidden" name="id" value="'.$this->param['id'].'">';
			echo '</form>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_submit_packing","Submit Packing");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Submit","fn.app.packing.packing.submit()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
