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
			global $os;
			$dbc = $this->dbc;
			$aPacking = json_decode($os->load_variable("aPacking","json"),true);
			//$repack = $dbc->GetRecord("repacks","*","id=".$this->param['id']);
			$items = isset($this->param['items'])?$this->param['items']:array();


			if(count($items)<2){
				echo 'โปรดเลือกมากกว่า 2!';
			}else{
				$select_packtype = '<select name="pack_name" class="form-control mr-2">';
				foreach($aPacking as $pack){
					$readonly = isset($pack['readonly'])?$pack['readonly']:true;
					$select_packtype .=  '<option data-value="'.$pack['value'].'" data-readonly="'.($readonly?"true":"false").'">'.$pack['name'].'</option>';
				}
				$select_packtype .= '</select>';
				
			
				echo '<form name="form_combinerepack" class="form-horizontal" role="form" onsubmit=";return false;">';
					//echo '<input type="hidden" name="id" value="'.$pack['id'].'">';
					echo '<div class="form-group row">';
						echo '<label class="col-sm-2 col-form-label text-right">หมายเลขถุง</label>';
						echo '<div class="col-sm-10">';
							echo '<input type="" class="form-control" name="code">';
						echo '</div>';
					echo '</div>';
					echo '<div class="form-group row">';
						echo '<label class="col-sm-2 col-form-label text-right">ประเภทถุง</label>';
						echo '<div class="col-sm-10">';
							echo $select_packtype;
						echo '</div>';
					echo '</div>';
					echo '<div class="form-group row">';
						echo '<label class="col-sm-2 col-form-label text-right">ขนิดถุง</label>';
						echo '<div class="col-sm-10">';
							echo '<select name="pack_type" class="form-control">';
								echo '<option>ถุงปกติ</option>';
								echo '<option>ถุงกระสอบ</option>';
							echo '</select>';
						echo '</div>';
					echo '</div>';
					echo '<div class="form-group row">';
						echo '<label class="col-sm-2 col-form-label text-right">น้ำหนักถุง</label>';
						echo '<div class="col-sm-10">';
							echo '<input class="form-control" name="weight_expected" placeholder="จำนวนที่ต้องการแบ่งใหม่" readonly>';
						echo '</div>';
					echo '</div>';
					echo '<div class="form-group row">';
						echo '<label class="col-sm-2 col-form-label text-right">น้ำหนักจริง</label>';
						echo '<div class="col-sm-10">';
							echo '<input class="form-control" name="weight_actual" placeholder="จำนวนที่ต้องการแบ่งใหม่" readonly>';
						echo '</div>';
					echo '</div>';
					$total_weight = 0;
					echo '<table class="table table-brodered">';
						echo '<thead>';
							echo '<tr>';
								echo '<th class="text-center">หมายเลขถุง</th>';
								echo '<th class="text-center">ประเภทถุง</th>';
								echo '<th class="text-center">ชนิดถุง</th>';
								echo '<th class="text-center">น้ำหนักถุง</th>';
								echo '<th class="text-center">น้ำหนักจริง</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($items as $item){
							$order = $dbc->GetRecord("bs_packing_items","*","id=".$item);
							echo '<tr>';
								echo '<td class="text-center">'.$order['code'].'</td>';
								echo '<td class="text-center">'.$order['pack_type'].'</td>';
								echo '<td class="text-center">'.$order['pack_name'].'</td>';
								echo '<td class="text-center">'.$order['weight_expected'].'</td>';
								echo '<td class="text-center">'.$order['weight_actual'].'</td>';
							echo "</tr>";
							$total_weight += $order['weight_actual'];
							echo '<input type="hidden" name="pack_id[]" value="'.$order['id'].'">';
						}
						echo '</tbody>';
					echo '</table>';
					echo '<input type="hidden" name="total_weight_actual" value="'.$total_weight.'">';
				echo '</form>';
			
				
			}
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_combine_repack","Combine Repack");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-danger","Combine","fn.app.repack.repack.combine()")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
