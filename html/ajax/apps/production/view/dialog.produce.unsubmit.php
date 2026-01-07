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
			$production = $dbc->GetRecord("bs_productions","*","id=".$this->param['id']);
			echo '<form name="form_unsubmitproduce">';
			echo '<input type="hidden" name="id" value="'.$production['id'].'">';
			echo '';
			echo '</fomr>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_unsubmit_produce","Unsubmit Production");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Unsubmit","fn.app.production.produce.unsubmit()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
