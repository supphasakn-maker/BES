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
			$map_item = $dbc->GetRecord("bs_mapping_usd_purchases","*","id=".$this->param['id']);
			$sql = "SELECT * FROM bs_mapping_usd_purchases WHERE purchase_id = ".$map_item['purchase_id'];
			$rst = $dbc->Query($sql);

			echo '<table class="table">';
				echo '<thead>';
					echo '<tr>';
						echo '<th class="text-center">ID</th>';
						echo '<th class="text-center">Mapping ID</th>';
						echo '<th class="text-center">Purchase ID</th>';
						echo '<th class="text-center">Amount</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				while($item = $dbc->Fetch($rst)){
					echo '<tr>';
						echo '<td class="text-center">'.$item['id'].'</td>';
						echo '<td class="text-center">'.$item['mapping_id'].'</td>';
						echo '<td class="text-center">'.$item['purchase_id'].'</td>';
						echo '<td class="text-center">'.$item['amount'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_lookup_usd","Lookup Mapping");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
