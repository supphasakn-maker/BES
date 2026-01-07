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
			$spot = $dbc->GetRecord("bs_purchase_spot","*","id=".$this->param['id']);
			
			
			$parent = $dbc->GetRecord("bs_purchase_spot","*","id=".$spot['parent']);
			$supplier = $dbc->GetRecord("bs_suppliers","*","id=".$spot['supplier_id']);
			
			echo '<h3>Splited From</h3>';
			echo '<table class="table table-bordered table-form">';
				echo '<tbody>';
				echo '<tr>';
					echo '<td><label>Date</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['confirm'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Supllier</label></td>';
					echo '<td><input class="form-control" readonly value="'.$supplier['name'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Type</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['type'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Amount</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['amount'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Spot Rate</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['rate_spot'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Premium/Discount</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['rate_pmdc'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Ref</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['ref'].'"></td>';
				echo '</tr>';
				echo '</tbody>';
			echo '</table>';
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_split_spot_view","Split View");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
