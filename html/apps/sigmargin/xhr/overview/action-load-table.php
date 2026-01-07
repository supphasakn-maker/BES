<?php
session_start();
include_once "../../../../config/define.php";
include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";
include_once "../../include/engine.php";

@ini_set('display_errors', 1);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$engine = new engine($dbc);

$oz = 32.1507;
$date = strtotime($_POST['date']);

?>
<table id="tblOverview" class="table table-bordered table-sm">
	<thead class="thead-dark">
		<tr>
			<td class="text-center"></td>
			<td class="text-center">B</td>
			<td class="text-center">S</td>
			<td class="text-center"></td>
			<td class="text-center">S</td>
			<td class="text-center">B</td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
			<td class="text-center"></td>
		</tr>
		<tr>
			<td class="text-center">Products</td>
			<td class="text-center">Balances</td>
			<td class="text-center">Ex rate</td>
			<td class="text-center">in USD equiv</td>
			<td class="text-center"></td>
			<td class="text-center">initial Margin</td>
			<td class="text-center"></td>
			<td class="text-center">Variation Margin</td>
			<td class="text-center"></td>
			<td class="text-center">Cut_loss</td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td class="text-center"><input name="value_initial" class="form-control form-control-sm text-center" type="text" value="10%" onchange="fn.app.sigmargin.overview.calculate()"></td>
			<td class="text-center"></td>
			<td class="text-center"><input name="value_variation" class="form-control form-control-sm text-center" type="text" value="7%" onchange="fn.app.sigmargin.overview.calculate()"></td>
			<td class="text-center"></td>
			<td class="text-center"><input name="value_cut_loss" class="form-control form-control-sm text-center" type="text" value="5%" onchange="fn.app.sigmargin.overview.calculate()"></td>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<table id="tblJournal" class="table table-bordered table-sm">
	<thead>
		<tr>
			<th class="text-center" rowspan="2">Date</th>
			<th class="text-center" colspan="3">USD</th>
			<th class="text-center" colspan="3">XAG</th>
			<th class="text-center" colspan="4">INCLUDED in available funds</th>

		</tr>
		<tr>
			<th class="text-center">DR</th>
			<th class="text-center">CR</th>
			<th class="text-center">BAL</th>
			<th class="text-center">DR</th>
			<th class="text-center">CR</th>
			<th class="text-center">BAL</th>
			<th class="text-center">Rollover</th>
			<th class="text-center">SPOT Sell</th>
			<th class="text-center">SPOT Buy</th>
			<th class="text-center">Cashจาก MetalsWeb</th>
			<th class="text-center">DF</th>
		</tr>
	</thead>
	<tbody>
		<?php

		$init_a = $os->load_variable("fInit_USD");
		$init_b = $os->load_variable("fInit_XAG");
		$aBalance = array(floatval($init_a), floatval($init_b));

		$xdata = $engine->get_balance($date);
		$aBalance[0] += $xdata[0];
		$aBalance[1] += $xdata[1];


		echo '<tr>';
		echo '<td class="text-center">Start Balance</td>';
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';
		echo $engine->show_tr($aBalance[0], 2, true, array());
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';
		echo $engine->show_tr($aBalance[1], 3, true, array());
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';
		echo '<td class="text-center"></td>';

		echo '';
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

			if ($dbc->HasRecord("bs_smg_daily", "date ='" . $date_sql . "'")) {
				$line = $dbc->GetRecord("bs_smg_daily", "*", "date ='" . $date_sql . "'");
				$spot_buy = $line['spot_buy'];
				$spot_sell = $line['spot_sell'];
				$cash = $line['cash'];
				$rollover = $line['rollover'];
			}

			/*
			$line = $dbc->GetRecord("bs_smg_rollover","SUM(amount),rate_spot","date ='".$date_sql."' AND type='Sell'");
			$spot_buy = $line[1];
			$rollover += $line[0];


			$line = $dbc->GetRecord("bs_smg_rollover","SUM(amount),rate_spot","trade ='".$date_sql."' AND type='Buy'");
			$spot_sell += -$line[1];
			*/
			/*
			$line = $dbc->GetRecord("bs_smg_rollover","SUM(amount),rate_spot","date ='".$date_sql."' AND type='Buy'");
			$rollover +=  $line[0];
*/


			$line = $dbc->GetRecord("bs_smg_cash", "SUM(amount)", "date ='" . $date_sql . "'");
			$cash += $line[0];


			$df = $aBalance[0] - $cash;

			$tooltip = ' data-toggle="tooltip" data-html="true"';
			echo '<tr>';
			echo '<td class="text-center">' . $date_display . '</td>';
			echo $engine->show_tr($usd_dr, 2, false, $usd_dr_msg);
			echo $engine->show_tr($usd_cr, 2, true, $usd_cr_msg);
			echo $engine->show_tr($aBalance[0], 2);
			echo $engine->show_tr($xag_dr, 2, false, $xag_dr_msg);
			echo $engine->show_tr($xag_cr, 2, true, $xag_cr_msg);
			echo $engine->show_tr($aBalance[1], 3);


			echo '<td class="text-right">' . number_format($rollover, 5) . '</td>';
			echo $engine->show_tr($spot_sell, 5);
			echo $engine->show_tr($spot_buy, 5);
			echo $engine->show_tr($cash, 2);
			echo '<td class="text-right">' . number_format($df, 2) . '</td>';

			echo '';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
<div class="">
	<input name="value_usd" value="<?php echo $aBalance[0]; ?>">
	<input name="value_xag" value="<?php echo $aBalance[1]; ?>">
</div>
<?php

if (empty($_GET['date'])) {
	$date_select = date("Y-m-d");
} else {
	$date_select = $_GET['date'];
}

$sql = "
        SELECT * FROM `bs_smg_payment` a INNER JOIN bs_smg_receiving b ON a.date = b.date
        WHERE  a.type = 'โอนมัดจำ' ORDER BY a.date DESC LIMIT 1
    ";

if ($result = $dbc->query($sql)) {
	while ($row = $result->fetch_row()) {
		// echo "row => ".print_r($row,true);
		$rs_bs_smg_trade = $dbc->GetRecord("bs_smg_trade", "*", "date='" . $row[1] . "' AND purchase_type = 'Buy' ORDER BY id DESC");
		$s = ((($row[4] + $row[3]) - ($row[8] + $row[9])) * 0.02);
		$usd_bal = ((($row[4] + $row[3]) - ($row[8] + $row[9])) * 0.02) - $rs_bs_smg_trade['amount'] * $s * $rs_bs_smg_trade['rate_spot'];
	}
}
?>

<?php
$dbc->Close();
?>