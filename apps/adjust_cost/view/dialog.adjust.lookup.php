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
		$adjust = $dbc->GetRecord("bs_adjust_cost", "*", "id=" . $this->param['id']);
?>
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-link active" id="nav-a-tab" data-toggle="tab" href="#nav-a" role="tab" aria-controls="nav-a" aria-selected="true">Overview</a>
				<a class="nav-link" id="nav-b-tab" data-toggle="tab" href="#nav-b" role="tab" aria-controls="nav-b" aria-selected="false">Buy Side</a>
				<a class="nav-link" id="nav-c-tab" data-toggle="tab" href="#nav-c" role="tab" aria-controls="nav-c" aria-selected="false">Sell</a>
				<a class="nav-link" id="nav-d-tab" data-toggle="tab" href="#nav-d" role="tab" aria-controls="nav-d" aria-selected="false">New Cost</a>
			</div>
		</nav>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-a" role="tabpanel" aria-labelledby="nav-a-tab">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-right">value_amount</th>
							<td class="text-right"><?php echo number_format($adjust['value_amount'], 4); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_buy</th>
							<td class="text-right"><?php echo number_format($adjust['value_buy'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_sell</th>
							<td class="text-right"><?php echo number_format($adjust['value_sell'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_new</th>
							<td class="text-right"><?php echo number_format($adjust['value_new'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_profit</th>
							<td class="text-right"><?php echo number_format($adjust['value_profit'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_adjust_cost</th>
							<td class="text-right"><?php echo number_format($adjust['value_adjust_cost'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_adjust_discount</th>
							<td class="text-right"><?php echo number_format($adjust['value_adjust_discount'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">value_net</th>
							<td class="text-right"><?php echo number_format($adjust['value_net'], 2); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="nav-b" role="tabpanel" aria-labelledby="nav-b-tab">
				<?php
				$sql = "SELECT * FROM bs_purchase_spot WHERE adjust_id = " . $adjust['id'] . " AND adjust_type='old'";
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Value Date</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Rate Spot</th>';
				echo '<th class="text-center">PMDC</th>';
				echo '<th class="text-center">USD Value (NoPMDC)</th>';
				echo '<th class="text-center">USD Value</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$total = array(0, 0, 0);
				while ($item = $dbc->Fetch($rst)) {
					$usd_value = $item['amount'] * ($item['rate_spot'] + $item['rate_pmdc']) * 32.1507;
					$usd_value_nopmdc = $item['amount'] * ($item['rate_spot']) * 32.1507;
					$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $item['supplier_id']);
					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $supplier['name'] . '</td>';
					echo '<td class="text-center">' . $item['value_date'] . '</td>';
					echo '<td class="text-center">' . $item['amount'] . '</td>';
					echo '<td class="text-center">' . $item['rate_spot'] . '</td>';
					echo '<td class="text-center">' . $item['rate_pmdc'] . '</td>';
					echo '<td class="text-center">' . number_format($usd_value_nopmdc, 4) . '</td>';
					echo '<td class="text-center">' . number_format($usd_value, 4) . '</td>';
					echo '<td class="text-center">' . $item['ref'] . '</td>';
					echo '</tr>';
					$total[0] += $item['amount'];
					$total[1] += $usd_value_nopmdc;
					$total[2] += $usd_value;
				}
				echo '</tbody>';
				echo '<tfoot>';
				echo '<tr>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[0], 4) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[1], 2) . '</th>';
				echo '<th class="text-center">' . number_format($total[2], 2) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '</tr>';
				echo '</tfoot>';
				echo '</table>';
				?>
			</div>
			<div class="tab-pane fade" id="nav-c" role="tabpanel" aria-labelledby="nav-c-tab">
				<?php
				$sql = "SELECT * FROM bs_sales_spot WHERE adjust_id = " . $adjust['id'];
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Value Date</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Rate Spot</th>';
				echo '<th class="text-center">USD Value (NoPMDC)</th>';
				echo '<th class="text-center">PMDC</th>';
				echo '<th class="text-center">USD Value</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$total = array(0, 0, 0);
				while ($item = $dbc->Fetch($rst)) {
					$usd_value = $item['amount'] * ($item['rate_spot'] + $item['rate_pmdc']) * 32.1507;
					$usd_value_nopmdc = $item['amount'] * ($item['rate_spot']) * 32.1507;
					$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $item['supplier_id']);
					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $supplier['name'] . '</td>';
					echo '<td class="text-center">' . $item['value_date'] . '</td>';
					echo '<td class="text-center">' . $item['amount'] . '</td>';
					echo '<td class="text-center">' . $item['rate_spot'] . '</td>';
					echo '<td class="text-center">' . $item['rate_pmdc'] . '</td>';
					echo '<td class="text-center">' . number_format($usd_value_nopmdc, 4) . '</td>';
					echo '<td class="text-center">' . number_format($usd_value, 4) . '</td>';
					echo '<td class="text-center">' . $item['ref'] . '</td>';
					echo '</tr>';
					$total[0] += $item['amount'];
					$total[1] += $usd_value_nopmdc;
					$total[2] += $usd_value;
				}
				echo '</tbody>';
				echo '<tfoot>';
				echo '<tr>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[0], 4) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[1], 2) . '</th>';
				echo '<th class="text-center">' . number_format($total[2], 2) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '</tr>';
				echo '</tfoot>';
				echo '</table>';
				?>
			</div>
			<div class="tab-pane fade" id="nav-d" role="tabpanel" aria-labelledby="nav-d-tab">
				<?php
				$sql = "SELECT * FROM bs_purchase_spot WHERE adjust_id = " . $adjust['id'] . " AND adjust_type='new'";
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Value Date</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Rate Spot</th>';
				echo '<th class="text-center">PMDC</th>';
				echo '<th class="text-center">USD Value (NoPMDC)</th>';
				echo '<th class="text-center">USD Value</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$total = array(0, 0, 0);
				while ($item = $dbc->Fetch($rst)) {
					$usd_value = $item['amount'] * ($item['rate_spot'] + $item['rate_pmdc']) * 32.1507;
					$usd_value_nopmdc = $item['amount'] * ($item['rate_spot']) * 32.1507;
					$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $item['supplier_id']);
					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $supplier['name'] . '</td>';
					echo '<td class="text-center">' . $item['value_date'] . '</td>';
					echo '<td class="text-center">' . $item['amount'] . '</td>';
					echo '<td class="text-center">' . $item['rate_spot'] . '</td>';
					echo '<td class="text-center">' . $item['rate_pmdc'] . '</td>';
					echo '<td class="text-center">' . number_format($usd_value_nopmdc, 4) . '</td>';
					echo '<td class="text-center">' . number_format($usd_value, 4) . '</td>';
					echo '<td class="text-center">' . $item['ref'] . '</td>';
					echo '</tr>';
					$total[0] += $item['amount'];
					$total[1] += $usd_value_nopmdc;
					$total[2] += $usd_value;
				}
				echo '</tbody>';
				echo '<tfoot>';
				echo '<tr>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[0], 4) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">-</th>';
				echo '<th class="text-center">' . number_format($total[1], 2) . '</th>';
				echo '<th class="text-center">' . number_format($total[2], 2) . '</th>';
				echo '<th class="text-center">-</th>';
				echo '</tr>';
				echo '</tfoot>';
				echo '</table>';
				?>
			</div>
		</div>
<?php


	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setModel("dialog_lookup", "Contract Lookup");
$modal->setExtraClass("modal-full");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
?>