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
			$packing = $dbc->GetRecord("bs_packings","*","id=".$this->param['id']);
			
			echo '<form name="form_editpacking" data-iterator="1">';
				echo '<input type="hidden" name="id" value="'.$this->param['id'].'">';
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
								"value" => $packing['date']
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "time",
								"type" => "time",
								"value" => $packing['time']
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
								),
								"value" => $packing['production_id']
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "weight_peritem",
								"class" => "text-center",
								"value" => $packing['weight_peritem']
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "total_item",
								"class" => "text-center",
								"value" => $packing['total_item']
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "total_weight",
								"class" => "text-center",
								"readonly" => true,
								"value" => $packing['total_weight']
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
								),
								"value" => $packing['size']
							));
						echo '</td>';
						echo '<td>';
							$iform->EchoItem(array(
								"name" => "remark",
								"value" => $packing['remark']
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
								),
								"value" => $packing['approver_weight']
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
								),
								"value" => $packing['approver_general']
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
						$sql = "SELECT * FROM bs_packing_items WHERE packing_id=".$packing['id'];
						$rst = $dbc->Query($sql);
						$i=1;
						while($item = $dbc->Fetch($rst)){
							echo '<tr>';
								echo '<td class="text-center">'.$i.'</td>';
								echo '<td><input type="text" class="form-control text-center" name="item_code[]" value="'.$item['code'].'"></td>';
								echo '<td><input type="text" readonly class="form-control text-center" name="item_weight[]"value="'.$item['weight_expected'].'"></td>';
								echo '<td><input type="text" class="form-control text-center" name="item_actual[]" value="'.$item['weight_actual'].'"></td>';
							echo '</tr>';
							$i++;
						}
						echo '</tbody>';
					echo '</table>';
				echo '</div>';
			echo '</form>';
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_edit_packing","Edit Packing");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Edit","fn.app.packing.packing.edit()")
	));
	$modal->setExtraClass("modal-full");
	$modal->EchoInterface();

	$dbc->Close();
?>
