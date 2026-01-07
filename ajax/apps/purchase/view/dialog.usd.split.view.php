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
			$usd = $dbc->GetRecord("bs_purchase_usd","*","id=".$this->param['id']);
			
			
			$parent = $dbc->GetRecord("bs_purchase_usd","*","id=".$usd['parent']);
			
			
			echo '<h3>Splited From</h3>';
			echo '<table class="table table-bordered table-form">';
				echo '<tbody>';
				echo '<tr>';
					echo '<td><label>Date</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['confirm'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Bank</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['bank'].'"></td>';
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
					echo '<td><label>Exchange Rate</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['rate_exchange'].'"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><label>Comment</label></td>';
					echo '<td><input class="form-control" readonly value="'.$parent['comment'].'"></td>';
				echo '</tr>';
				
				echo '</tbody>';
			echo '</table>';
			
			
		}
	}

	$modal = new myModel($dbc,$os->auth);
	$modal->setParam($_POST);
	$modal->setModel("dialog_split_usd_view","Split View");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss")
	));
	$modal->EchoInterface();

	$dbc->Close();
?>
