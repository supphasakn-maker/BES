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
		$transfer = $dbc->GetRecord("bs_transfers", "*", "id=" . $this->param['id']);
?>
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-link active" id="nav-a-tab" data-toggle="tab" href="#nav-a" role="tab" aria-controls="nav-a" aria-selected="true">Overview</a>
				<a class="nav-link" id="nav-b-tab" data-toggle="tab" href="#nav-b" role="tab" aria-controls="nav-b" aria-selected="false">Defered Spot</a>
				<a class="nav-link" id="nav-c-tab" data-toggle="tab" href="#nav-c" role="tab" aria-controls="nav-c" aria-selected="false">USD Purchase</a>
				<a class="nav-link" id="nav-d-tab" data-toggle="tab" href="#nav-d" role="tab" aria-controls="nav-d" aria-selected="false">Import</a>

			</div>
		</nav>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-a" role="tabpanel" aria-labelledby="nav-a-tab">
				<?php
				$sql = "SELECT SUM(amount*(rate_spot)*32.1507) FROM bs_purchase_spot WHERE transfer_id = " . $transfer['id'];
				$rst = $dbc->Query($sql);
				$line = $dbc->Fetch($rst);
				$total_defered = $line[0];

				$sql = "SELECT SUM(bs_purchase_spot.amount*(bs_purchase_spot.rate_spot+bs_purchase_spot.rate_pmdc)*32.1507) 
					FROM bs_purchase_spot 
					LEFT JOIN bs_imports ON bs_purchase_spot.import_id = bs_imports.id
					WHERE bs_imports.transfer_id = " . $transfer['id'];

				$rst = $dbc->Query($sql);
				$line = $dbc->Fetch($rst);
				$total_real_purchase = $line[0];

				echo '<table class="table table-bordered table-stripe">';
				echo '<tbody>';
				echo '<tr>';
				echo '<th class="text-center">USD Good Values</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_goods'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Deposit</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_deposit'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Paid</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_paid'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Adjusted</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_adjusted'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Total</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_total'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Fixed</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_fixed'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">USD Non Fixed</th>';
				echo '<td class="text-right">' . number_format($transfer['value_usd_nonfixed'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">THB Fixed</th>';
				echo '<td class="text-right">' . number_format($transfer['value_thb_fixed'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">THB Premium</th>';
				echo '<td class="text-right">' . number_format($transfer['value_thb_premium'], 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">THB Net</th>';
				echo '<td class="text-right">' . number_format($transfer['value_thb_net'], 2) . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<th class="text-center">Total Defered</th>';
				echo '<td class="text-right">' . number_format($total_defered, 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">Total Import</th>';
				echo '<td class="text-right">' . number_format($total_real_purchase, 2) . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<th class="text-center">Margin</th>';
				echo '<td class="text-right">' . number_format($total_defered - $total_real_purchase, 2) . '</td>';
				echo '</tr>';
				echo '</tbody>';
				echo '</table>';

				?>
			</div>
			<div class="tab-pane fade" id="nav-b" role="tabpanel" aria-labelledby="nav-b-tab">
				<?php
				$sql = "SELECT * FROM bs_purchase_spot 
					LEFT JOIN bs_transfer_usd ON bs_purchase_spot.id = bs_transfer_usd.purchase_id
					WHERE bs_transfer_usd.transfer_id = " . $transfer['id'];
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
				echo '<th class="text-center">USD Value</th>';
				echo '<th class="text-center">Ref</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				while ($item = $dbc->Fetch($rst)) {
					$usd_value = $item['amount'] * ($item['rate_spot'] + $item['rate_pmdc']) * 32.1507;
					$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $item['supplier_id']);
					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $supplier['name'] . '</td>';
					echo '<td class="text-center">' . $item['value_date'] . '</td>';
					echo '<td class="text-center">' . $item['amount'] . '</td>';
					echo '<td class="text-center">' . $item['rate_spot'] . '</td>';
					echo '<td class="text-center">' . $item['rate_pmdc'] . '</td>';
					echo '<td class="text-center">' . number_format($usd_value, 4) . '</td>';
					echo '<td class="text-center">' . $item['ref'] . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				?>
			</div>
			<div class="tab-pane fade" id="nav-c" role="tabpanel" aria-labelledby="nav-c-tab">
				<?php
				$sql = "SELECT * FROM bs_transfer_usd WHERE transfer_id = " . $transfer['id'];
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Date</th>';
				echo '<th class="text-center">Bank Date</th>';
				echo '<th class="text-center">Preminum Date</th>';
				echo '<th class="text-center">Forward Contract No.</th>';
				echo '<th class="text-center">rate_exchange</th>';
				echo '<th class="text-center">Preminum</th>';
				echo '<th class="text-center">Fx Rate+ Premium</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">THB</th>';
				echo '<th class="text-center">THB+Premium</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				while ($item = $dbc->Fetch($rst)) {
					$purchase = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $item['purchase_id']);
					$thb = $purchase['rate_finance'] * $purchase['amount'];


					echo '<tr>';
					echo '<td class="text-center">' . $purchase['id'] . '</td>';
					echo '<td class="text-center">' . $purchase['date'] . '</td>';
					echo '<td class="text-center">' . $item['premium_start'] . '</td>';
					echo '<td class="text-center">' . $item['premium_end'] . '</td>';
					echo '<td class="text-center">' . $item['fw_contract_no'] . '</td>';
					echo '<td class="text-center">' . $purchase['rate_finance'] . '</td>';
					echo '<td class="text-center">' . $item['premium'] . '</td>';
					echo '<td class="text-center">' . ($purchase['rate_finance'] + $item['premium']) . '</td>';

					echo '<td class="text-center">' . $purchase['amount'] . '</td>';
					echo '<td class="text-center">' . $thb . '</td>';
					echo '<td class="text-center">' . ($thb + $item['premium']) . '</td>';

					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				?>
			</div>
			<div class="tab-pane fade" id="nav-d" role="tabpanel" aria-labelledby="nav-d-tab">
				<?php
				$sql = "SELECT * FROM bs_imports WHERE transfer_id = " . $transfer['id'];
				$rst = $dbc->Query($sql);

				echo '<table class="table table-bordered table-stripe">';
				echo '<thead>';
				echo '<tr>';
				echo '<th class="text-center">ID</th>';
				echo '<th class="text-center">Parent</th>';
				echo '<th class="text-center">Delivery Date</th>';
				echo '<th class="text-center">Supplier</th>';
				echo '<th class="text-center">Amount</th>';
				echo '<th class="text-center">Delivery By</th>';
				echo '<th class="text-center">Type</th>';
				echo '<th class="text-center">Comment</th>';
				echo '<th class="text-center">Action</th>';
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				while ($item = $dbc->Fetch($rst)) {

					echo '<tr>';
					echo '<td class="text-center">' . $item['id'] . '</td>';
					echo '<td class="text-center">' . $item['parent'] . '</td>';
					echo '<td class="text-center">' . $item['delivery_date'] . '</td>';
					echo '<td class="text-center">' . $item['supplier_id'] . '</td>';
					echo '<td class="text-center">' . $item['amount'] . '</td>';
					echo '<td class="text-center">' . $item['delivery_by'] . '</td>';
					echo '<td class="text-center">' . $item['type'] . '</td>';
					echo '<td class="text-center">' . $item['comment'] . '</td>';
					echo '<td class="text-center"><button class="btn btn-danger btn-sm" onclick="fn.app.forward_contract.import.unmap(' . $item['id'] . ')">Unmap</button></td>';

					echo '</tr>';
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