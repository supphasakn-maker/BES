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
			$prepare = $dbc->GetRecord("bs_stock_prepare","*","id=".$this->param['id']);
			echo '<table class="table table-bordered">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">รายการ</th>';
						echo '<th class="text-center">ขนาด</th>';
						echo '<th class="text-center">จำนวน</th>';
						echo '<th class="text-center">รวมเป็น (กิโลกรัม)</th>';
						echo '<th class="text-center">หมายเหตุ</th>';
					echo '</tru>';
				echo '</thead>';
			echo '<tbody>';
				$total = 0;
				$sql = "SELECT * FROM bs_stock_items WHERE prepare_id = ".$prepare['id'];
				
					$rst = $dbc->Query($sql);
					while($line = $dbc->Fetch($rst)){
						echo '<tr>';
							echo '<td class="text-left">'.$line['name'].'</td>';
							echo '<td class="text-center">'.number_format($line['size'],0).'</td>';
							echo '<td class="text-center">'.number_format($line['amount'],0).'</td>';
							echo '<td class="text-center">'.number_format($line['size']*$line['amount'],0).'</td>';
							echo '<td class="text-center">'.$line['comment'].'</td>';
							$total += $line['size']*$line['amount'];
						echo '</tr>';
					}
			echo '</tbody>';
			echo '<tfoot>';
				echo '<tr>';
					echo '<th colspan="3"></th>';
					echo '<th>'.number_format($total,0).'</th>';
					echo '<th></th>';
				echo '</tr>';
			echo '</tfoot>';
			echo '</table>';
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_view_packing","View");
	$modal->setExtraClass("modal-full");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
