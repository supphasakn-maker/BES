<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();

$os = new oceanos($dbc);

class myModel extends imodal
{
	function body()
	{
		$dbc = $this->dbc;
		$usd = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $this->param['id']);

		echo '<table class="table table-bodered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">Date</th>';
		echo '<th class="text-center">Interest Rate</th>';
		echo '<th class="text-center">Interest Day</th>';
		echo '<th class="text-center">Interest</th>';
		echo '<th class="text-center">Paid THB</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		$total_interest = 0;
		$total_paid = 0;
		$sql = "SELECT * FROM bs_usd_payment WHERE purchase_id = " . $usd['id'];
		$rst = $dbc->Query($sql);
		while ($payment = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $payment['date'] . '</td>';
			echo '<td class="text-center">' . $payment['rate_interest'] . '</td>';
			echo '<td class="text-center">' . $payment['interest_day'] . '</td>';
			echo '<td class="text-center">' . $payment['interest'] . '</td>';
			echo '<td class="text-center">' . $payment['paid_thb'] . '</td>';
			echo '</tr>';
			$total_interest += $payment['interest'];
			$total_paid += $payment['paid_thb'];
		}
		echo '</tbody>';
		echo '<tfoot>';
		echo '<tr>';
		echo '<th class="text-right" colspan="3">Total</th>';
		echo '<th class="text-center">' . $total_interest . '</th>';
		echo '<th class="text-center">' . $total_paid . '</th>';
		echo '</tr>';
		echo '</tfoot>';
		echo '</table>';
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_interest", "Interest Report");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
