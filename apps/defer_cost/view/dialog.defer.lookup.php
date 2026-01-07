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
		$adjust = $dbc->GetRecord("bs_defer_cost", "*", "id=" . $this->param['id']);
?>
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-link active" id="nav-a-tab" data-toggle="tab" href="#nav-a" role="tab" aria-controls="nav-a" aria-selected="true">Overview</a>
				<a class="nav-link" id="nav-b-tab" data-toggle="tab" href="#nav-b" role="tab" aria-controls="nav-b" aria-selected="false">Buy Side</a>
				<a class="nav-link" id="nav-c-tab" data-toggle="tab" href="#nav-c" role="tab" aria-controls="nav-c" aria-selected="false">Defer Spot</a>
			</div>
		</nav>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-a" role="tabpanel" aria-labelledby="nav-a-tab">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th class="text-right">Value Defer Spot</th>
							<td class="text-right"><?php echo number_format($adjust['value_defer_spot'], 4); ?></td>
						</tr>
						<tr>
							<th class="text-right">Value Purchase</th>
							<td class="text-right"><?php echo number_format($adjust['value_net'], 2); ?></td>
						</tr>
						<tr>
							<th class="text-right">Defer Match</th>
							<td class="text-right"><?php echo number_format($adjust['defer'], 2); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="nav-b" role="tabpanel" aria-labelledby="nav-b-tab">
				<?php
				$sql = "SELECT * FROM bs_purchase_spot WHERE defer_id = " . $adjust['id'] . " ";
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
				$sql = "SELECT * FROM bs_incoming_plans WHERE defer_id = " . $adjust['id'];
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">USD Value</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				$total = array(0, 0);
				while ($item = $dbc->Fetch($rst)) {
					$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $item['supplier_id']);
					$product_name = $dbc->GetRecord("bs_products", "*", "id=" . $item['product_type_id']);
					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $supplier['name'] . '</td>';
					echo '<td class="text-center">' . number_format($item['amount'], 4) . '</td>';
					echo '<td class="text-center">' . number_format($item['usd'], 4) . '</td>';
					echo '<td class="text-center">' . $product_name['name'] . '</td>';
					echo '</tr>';
					$total[0] += $item['amount'];
					$total[1] += $item['usd'];
				}
				echo '</tbody>';
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