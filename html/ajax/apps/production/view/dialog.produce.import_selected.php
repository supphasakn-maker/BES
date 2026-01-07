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
			echo '<table class="table table-sm table-bordered">';
				echo '<thead>';
					echo '<tr>';
						echo '<th></th>';
						echo '<th>เวลาเริ่มชั่งแท่งเงิน</th>';
						echo '<th>เลขที่ใบขน</th>';
						echo '<th>จำนวนแท่งเข้า</th>';
						echo '<th>น้ำหนักสุทธิ</th>';
						echo '<th>น้ำหนักที่ชั่ง</th>';
						echo '<th>น้ำหนัก ขาด/เกิน</th>';
						echo '<th>น้ำหนักเฉลี่ย</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$json = json_decode($production['data'],true);
				foreach($json['import'] as $item){
					echo '<tr>';
						echo '<td><input type="radio" name="import_item"></td>';
						echo '<td class="text-center" yname="import_time"> '.$item['import_time'].'</td>';
						echo '<td class="text-center" yname="import_number">'.$item['import_number'].'</td>';
						echo '<td class="text-center" yname="import_bar">'.$item['import_bar'].'</td>';
						echo '<td class="text-center" yname="import_weight_in">'.$item['import_weight_in'].'</td>';
						echo '<td class="text-center" yname="import_weight_actual">'.$item['import_weight_actual'].'</td>';
						echo '<td class="text-center" yname="import_weight_margin">'.$item['import_weight_margin'].'</td>';
						echo '<td class="text-center" yname="import_weight_bar">'.$item['import_weight_bar'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_import_selected","Select Import");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Select","fn.app.production.produce.select_import(".$_POST['import_id'].")")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
