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
			$pack_item = $dbc->GetRecord("bs_packing_items","*","id=".$this->param['id']);
			echo '<form name="form_splitrepack" data-iterator="1">';
				echo '<input type="hidden" name="id" value="'.$this->param['id'].'">';
				echo '<table class="table">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center">รหัส</th>';
							echo '<th class="text-center">น้ำหนักเดิม</th>';
							echo '<th class="text-center" size="30px">ต้องการแบ่ง</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						echo '<tr>';
							echo '<td class="text-center">'.$pack_item['code'].'</td>';
							echo '<td class="text-center">'.$pack_item['weight_actual'].'</td>';
							echo '<td><input class="form-control text-center" type="number" name="spliter" value="2"></td>';
						echo '</tr>';
					echo '</tbody>';
				echo '</table>';
				echo '<hr>';
				echo '<table id="tblSpliter" class="table table-sm">';
					echo '<thead>';
						echo '<tr>';
							echo '<th class="text-center">ลำดับ</th>';
							echo '<th class="text-center">รหัส</th>';
							echo '<th class="text-center">น้ำหนัก</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					echo '</tbody>';
				echo '</table>';
			echo '</form>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_split_repack","Split Repack");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Split","fn.app.packing.repack.split()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
