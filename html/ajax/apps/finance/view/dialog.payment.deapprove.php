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
			$payment = $dbc->GetRecord("bs_payments","*","id=".$this->param['id']);
			echo "Are sure to approve";
			echo '<form name="form_approvepayment">';
				echo '<input type="hidden" name="id" value="'.$payment['id'].'">';
			echo '</form>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_approve_payment","DeApprove Payment");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Approve","fn.app.finance.payment.deapprove()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
