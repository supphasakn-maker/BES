<?php
include_once "../../sigmargin_stx/include/engine.php";

$engine = new engine($dbc);

$oz = 32.1507;
$date = strtotime($_POST['date']);

?>
<table id="tblJournal" class="table table-bordered table-sm">
	<thead>
		<tr>
			<th class="text-center" rowspan="2">Date</th>
			<th class="text-center" colspan="3">USD</th>
			<th class="text-center" rowspan="2">XAG Balance</th>

		</tr>
		<tr>
			<th class="text-center">Balance</th>
			<th class="text-center">Rate</th>
			<th class="text-center">Interest</th>
		</tr>
	</thead>
	<tbody>
		<?php

		$init_a = $os->load_variable("fInit_USD");
		$init_b = $os->load_variable("fInit_XAG");
		$aBalance = array(floatval($init_a), floatval($init_b), 0.0);

		$xdata = $engine->get_balance($date);
		$aBalance[0] += $xdata[0];
		$aBalance[1] += $xdata[1];

		$line = $dbc->GetRecord("bs_smg_stx_rate", "*", "date < '" . date("Y-m-1", $date) . "' ORDER BY date DESC");
		$interest_rate = $line['rate'];
		echo '<tr>';
		echo '<td class="text-center">Start Balance</td>';
		echo $engine->show_tr($aBalance[0], 2, true, array());
		echo '</tr>';

		for ($d = 1; $d <= date("t", $date); $d++) {
			$date_sql = date("Y-m-", $date) . sprintf("%02d", $d);
			$date_display = sprintf("%02d", $d) . date("/m/Y", $date);

			$usd_cr = 0;
			$usd_dr = 0;
			$xag_cr = 0;
			$xag_dr = 0;
			$rollover = 0;
			$spot_sell = 0;
			$spot_buy = 0;
			$cash = 0;

			$usd_dr_msg = array();
			$usd_cr_msg = array();
			$xag_dr_msg = array();
			$xag_cr_msg = array();
			$xag_cr_msg = array();
			$xag_cr_msg = array();

			//USD-
			$tdata = $engine->get_usd_debit($date_sql);
			foreach ($tdata[1] as $msg) {
				array_push($usd_dr_msg, $msg);
			}
			$usd_dr += -$tdata[0];

			//USD+
			$tdata = $engine->get_usd_credit($date_sql);
			foreach ($tdata[1] as $msg) {
				array_push($usd_cr_msg, $msg);
			}
			$usd_cr += $tdata[0];

			$aBalance[0] = $aBalance[0] + $usd_dr + $usd_cr;

			$tdata = $engine->get_xag_debit($date_sql);
			foreach ($tdata[1] as $msg) {
				array_push($xag_dr_msg, $msg);
			}
			$xag_dr += -$tdata[0];

			//USD+
			$tdata = $engine->get_xag_credit($date_sql);
			foreach ($tdata[1] as $msg) {
				array_push($xag_cr_msg, $msg);
			}
			$xag_cr += $tdata[0];

			$aBalance[1] += $xag_cr + $xag_dr;

			if ($dbc->HasRecord("bs_smg_stx_daily", "date ='" . $date_sql . "'")) {
				$line = $dbc->GetRecord("bs_smg_stx_daily", "*", "date ='" . $date_sql . "'");
				$spot_buy = $line['spot_buy'];
				$spot_sell = $line['spot_sell'];
				$cash = $line['cash'];
				$rollover = $line['rollover'];
			}

			/*
			$line = $dbc->GetRecord("bs_smg_stx_rollover","SUM(amount),rate_spot","date ='".$date_sql."' AND type='Sell'");
			$spot_buy = $line[1];
			$rollover += $line[0];


			$line = $dbc->GetRecord("bs_smg_stx_rollover","SUM(amount),rate_spot","trade ='".$date_sql."' AND type='Buy'");
			$spot_sell += -$line[1];
			*/
			/*
			$line = $dbc->GetRecord("bs_smg_stx_rollover","SUM(amount),rate_spot","date ='".$date_sql."' AND type='Buy'");
			$rollover +=  $line[0];
			*/


			$line = $dbc->GetRecord("bs_smg_stx_cash", "SUM(amount)", "date ='" . $date_sql . "'");
			$cash += $line[0];


			$df = $aBalance[0] - $cash;

			if ($dbc->HasRecord("bs_smg_stx_rate", "date = '" . $date_sql . "'")) {
				$line = $dbc->GetRecord("bs_smg_stx_rate", "*", "date = '" . $date_sql . "'");
				$interest_rate = $line['rate'];
				$interest_rate_short = $line['rate_short'];
			}
			if ($aBalance[0] < 0) {
				$interest = $aBalance[0] * ($interest_rate_short / 100) / 360;
			} else {
				$interest = $aBalance[0] * ($interest_rate / 100) / 360;
			}

			$aBalance[2] += $interest;


			$tooltip = ' data-toggle="tooltip" data-html="true"';
			echo '<tr>';
			echo '<td class="text-center">' . $date_display . '</td>';
			echo $engine->show_tr($aBalance[0], 2);
			if ($aBalance[0] < 0) {
				echo '<td class="text-center">' . number_format($interest_rate_short, 2) . '</td>';
			} else {
				echo '<td class="text-center">' . number_format($interest_rate, 2) . '</td>';
			}

			if ($aBalance[0] < 0) {
				echo $engine->show_tr($aBalance[0] * ($interest_rate_short / 100) / 360, 2);
			} else {
				echo $engine->show_tr($aBalance[0] * ($interest_rate / 100) / 360, 2);
			}

			echo $engine->show_tr($aBalance[1], 2);


			echo '';
			echo '</tr>';
		}
		?>
	</tbody>
	<thead>
		<tr>
			<td class="text-right" colspan="3">Total</td>
			<?php echo $engine->show_tr($aBalance[2], 2); ?>
			<td class="text-center"></td>
		</tr>
		<?php
		if ($dbc->HasRecord("bs_smg_stx_cash", "DATE_FORMAT(date, '%Y-%m') = '" . date("Y-m", $date) . "'")) {
			$line = $dbc->GetRecord("bs_smg_stx_cash", "amount", "DATE_FORMAT(date, '%Y-%m')='" . date("Y-m", $date) . "'");
			$cash = $line['amount'];
			echo '<tr>';
			echo '<td class="text-right" colspan="3">Metal Web</td>';
			echo $engine->show_tr($cash, 2);
			echo '<td class="text-center"></td>';
			echo '</tr>';
		}

		?>
	</thead>
</table>