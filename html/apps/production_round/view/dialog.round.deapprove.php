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
			$round = $dbc->GetRecord("bs_productions_round","*","id=".$this->param['id']);
			echo "Are sure to deapprove";
			echo '<form name="form_approveround">';
				echo '<input type="hidden" name="id" value="'.$round['id'].'">';
                echo '<input type="text" class="form-control" name="amount" value="'.$round['amount'].'">';
			echo '</form>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_approve_round","DeApprove Round");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Approve","fn.app.production_round.round.deapprove()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
