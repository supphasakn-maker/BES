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
			$iform = new iform($dbc,$this->auth);
			//$packing = $dbc->GetRecord("packings","*","id=".$this->param['id']);
			
			echo '<form name="form_addpacking" data-iterator="1">';
				echo '<table class="table table-sm">';
					echo '<tr>';
						echo '<th class="text-center">วันที่จัดแพ็ค</th>';
						echo '<th class="text-center">เวลา</th>';
						echo '<th class="text-center">รอบที่</th>';
						echo '<th class="text-center">น้ำหนักต่อถุง</th>';
						echo '<th class="text-center">จำนวนถุง</th>';
						echo '<th class="text-center">รวม</th>';
						echo '<th class="text-center">ขนาด</th>';
						echo '<th class="text-center">Remark</th>';
						echo '<th class="text-center">ผู้ชั่ง</th>';
						echo '<th class="text-center">ผู้ตรวจ</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "date",
								"type" => "date",
								"value" => date("Y-m-d")
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "time",
								"type" => "time",
								"value" => date("H:i")
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "production_id",
								"type" => "comboboxdb",
								"source" => array(
									"name" => "round",
									"value" => "id",
									"table" => "bs_productions"
								)
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "weight_peritem",
								"class" => "text-center",
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "total_item",
								"class" => "text-center",
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "total_weight",
								"class" => "text-center",
								"readonly" => true
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "size",
								"type" => "combobox",
								"source" => array(
									"เม็ดเล็ก",
									"เม็ดใหญ่",
									"เม๊ดกล่อง"
								)
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "remark"
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "approver_weight",
								"type" => "comboboxdb",
								"source" => array(
									"name" => "fullname",
									"value" => "id",
									"table" => "bs_employees"
								)
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "approver_general",
								"type" => "comboboxdb",
								"source" => array(
									"name" => "fullname",
									"value" => "id",
									"table" => "bs_employees"
								)
							));
						echo '</td>';
					echo '</tr>';
				echo '</table>';
				echo '<hr>';
				echo '<hr>';
				echo '<div class="container">';
					echo '<table id="tblPackitem" class="table table-bordered">';
						echo '<thead>';
							echo '<tr>';
								echo '<th class="text-center">ลำดับ</th>';
								echo '<th class="text-center">รหัสถุง</th>';
								echo '<th class="text-center">นำหนัก</th>';
								echo '<th class="text-center">น้ำหนักแพ็คเสร็จ</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			
				echo '';
				echo '';
				echo '';
				echo '';
				echo '';
			echo '</form>';
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_add_packing","Add Packing");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Add","fn.app.packing.packing.add()")
	));
	$modal->setExtraClass("modal-full");
	$modal->EchoInterface();

	$dbc->Close();
?>
