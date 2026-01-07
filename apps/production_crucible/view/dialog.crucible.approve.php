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
			$crucible = $dbc->GetRecord("bs_productions_crucible","*","id=".$this->param['id']);
			echo '<form name="form_approvecrucible">';
				echo '<input type="hidden" name="id" value="'.$crucible['id'].'">';
				echo '<table class="table table-bordered table-form">';
				echo '<tbody>';
					echo '<tr>';
						echo '<td><label>เวลาที่ต้องการปิด</label></td>';
						echo '<td><input name="submited" type="datetime-local" class="form-control" value="'.date("Y-m-d H:i:s").'"></td>';
					echo '</tr>';
				echo '</tbody>';
				echo '</table>';
			echo '</fomr>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_approve_crucible","ยกเลิกการใช้เบ้า");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Approve","fn.app.production_crucible.crucible.approve()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
