<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";


@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$show_all = isset($_POST['chkShowCompleted']) ? true : false;

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
if ($_POST['bank'] == "BBL") {
	$credit = 350000000;
} else if ($_POST['bank'] == "KBANK") {
	$credit = 10000000;
} else if ($_POST['bank'] == "BAY") {
	$credit = 70000000;
} else {
	$credit = 220000000;
}

$today = time();
//$today = strtotime("2022-08-23");


$sql = "SELECT
		SUM(bs_transfer_payments.paid) AS paid,
		SUM(bs_transfer_payments.interest) AS interest
	FROM bs_transfer_payments
	LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id
	WHERE
		bs_transfers.bank ='" . $_POST['bank'] . "'
		AND bs_transfers.date = '" . date("Y-m-d", $today) . "'
		AND bs_transfer_payments.currency = 'THB'";
$rst = $dbc->Query($sql);
$today_payment = $dbc->Fetch($rst);

echo '<table class="table table-sm table-bordered">';
echo '<tbody>';
echo '<tr>';
echo '<td class="text-center" colspan="5"></td>';
echo '<td class="text-center" colspan="3">Today ' . date("Y-m-d", $today) . '</td>';

echo '</tr>';
echo '<tr class="bg-dark text-white">';
echo '<td class="text-center">Credit</td>';
echo '<td class="text-center">Total THB Value (Nonfixed x Counter Rate)</td>';
echo '<td class="text-center">Total Paid</td>';
echo '<td class="text-center">Unpaid TR</td>';
echo '<td class="text-center">Net Credit</td>';
echo '<td class="text-center">Today Payment</td>';
echo '<td class="text-center">Add TR</td>';
echo '<td class="text-center">Interest Expense</td>';
echo '</tr>';
echo '<tr>';
echo '<td class="text-center">' . number_format($credit, 2) . '</td>';

$sql = "SELECT
						SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
						SUM(value_thb_net) AS net,
						SUM(paid_thb) AS paid,
						SUM(paid_usd * rate_counter) AS paid_usd,
						SUM(value_usd_nonfixed * rate_counter) AS value_nonfixed_thb,
						COUNT(bs_transfers.id) AS total_tr
					FROM bs_transfers
					WHERE bs_transfers.bank ='" . $_POST['bank'] . "'";
$rst = $dbc->Query($sql);
$transfer = $dbc->Fetch($rst);
echo '<td class="text-center">' . number_format($transfer['net'], 2) . '(' . number_format($transfer['value_nonfixed_thb'], 2) . ')</td>';
echo '<td class="text-center">' . number_format($transfer['paid'], 2) . '</td>';
echo '<td class="text-center">' . number_format($transfer['unpaid'] + $transfer['value_nonfixed_thb'] - $transfer['paid_usd'], 2) . '</td>';

$total_remain_credit = $credit - $transfer['unpaid'] - $transfer['value_nonfixed_thb'] + $transfer['paid_usd'];
echo '<td class="text-center">' . number_format($total_remain_credit, 2) . '</td>';

$sql = "SELECT
						SUM(bs_transfers.value_thb_net-paid_thb) AS unpaid,
						SUM(paid_thb) AS paid,
						COUNT(bs_transfers.id) AS total_tr
					FROM bs_transfers
					WHERE
						bs_transfers.bank ='" . $_POST['bank'] . "'
						AND bs_transfers.date = '" . date("Y-m-d") . "'";
$rst = $dbc->Query($sql);
$transfer = $dbc->Fetch($rst);
echo '<td class="text-center">' . number_format($transfer['paid'], 2) . '</td>';
echo '<td class="text-center">' . $transfer['total_tr'] . '</td>';

$sql = "SELECT
					SUM(bs_transfer_payments.interest) AS interest
				FROM bs_transfer_payments
				LEFT JOIN bs_transfers ON bs_transfer_payments.transfer_id = bs_transfers.id
				WHERE
					bs_transfers.bank ='" . $_POST['bank'] . "'
					AND bs_transfer_payments.date ='" . date("Y-m-d", $today) . "'
					AND bs_transfer_payments.currency = 'THB'
					";
$rst = $dbc->Query($sql);
$payment = $dbc->Fetch($rst);


echo '<td class="text-center">' . number_format($payment['interest'], 2) . '</td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';

echo '<table id="tblTRMain" class="table table-sm table-bordered">';
echo '<thead>';
echo '<tr class="bg-dark text-white">';
echo '<th class="text-center"></th>';
echo '<th class="text-center">TR Date</th>';
echo '<th class="text-center">TR Code</th>';
echo '<th class="text-center">Remark</th>';
echo '<th class="text-center">Rate Interest</th>';
echo '<th class="text-center">Due Date</th>';
echo '<th class="text-center">Type</th>';
echo '<th class="text-center">Supplier</th>';

echo '<th class="text-center">Total USD Value</th>';
echo '<th class="text-center">USD Fixed Value</th>';
echo '<th class="text-center">USD Non-Fixed Value</th>';
echo '<th class="text-center">Total THB Net</th>';

echo '<th class="text-center">USD Paid</th>';
echo '<th class="text-center">THB Paid</th>';
echo '<th class="text-center">Interest</th>';
echo '<th class="text-center">Action</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
$data = array();
$sql = "SELECT * FROM bs_transfers WHERE bank ='" . $_POST['bank'] . "' AND date BETWEEN '" . $_POST['from'] . "' AND '" . $_POST['to'] . "' ORDER BY " . $_POST['order_by'] . " " . $_POST['order_type'] . "";
$rst = $dbc->Query($sql);
while ($transfer = $dbc->Fetch($rst)) {
	$supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $transfer['supplier_id']);
	$payment = $dbc->GetRecord("bs_transfer_payments", "SUM(interest),SUM(paid)", "transfer_id=" . $transfer['id']);
	$show_tr = true;
	$tr_class = "";
	if (
		number_format($transfer['value_thb_net'], 2) == number_format($transfer['paid_thb'], 2) &&
		number_format($transfer['value_usd_nonfixed'], 2) == number_format($transfer['paid_usd'], 2)
	) {
		$tr_class = "bg-warning";
		if (!$show_all) $show_tr = false;
	}
	if ($show_tr) {
		echo '<tr class="' . $tr_class . '">';
		echo '<td class="text-center">';
		echo '<button class="btn btn-sm btn-primary" onclick="$(this).parent().parent().next().toggleClass(\'d-none\')">Toggle</button>';
		echo '</td>';
		echo '<td class="text-center">' . $transfer['date'] . '</td>';
		echo '<td class="text-center">' . $transfer['code'] . '</td>';
		echo '<td class="text-center">' . $transfer['remark'] . '</td>';
		echo '<td class="text-center">' . $transfer['rate_interest'] . '</td>';
		echo '<td class="text-center">' . $transfer['due_date'] . '</td>';
		echo '<td class="text-center">' . $transfer['type'] . '</td>';
		echo '<td class="text-center">' . $supplier['name'] . '</td>';

		echo '<td class="text-center">' . number_format($transfer['value_usd_total'] + $transfer['value_usd_paid'], 2) . '</td>';
		echo '<td class="text-center">' . number_format($transfer['value_usd_fixed'], 2) . '</td>';
		$total_usd_paid = $transfer['paid_usd'] + $transfer['value_usd_paid'];
		echo '<td class="text-center">' . number_format($transfer['value_usd_nonfixed'] - $total_usd_paid, 2) . '</td>';
		echo '<td class="text-center">' . number_format($transfer['value_thb_net'], 2) . '</td>';
		echo '<td class="text-center">' . number_format($transfer['paid_usd'] + $transfer['value_usd_paid'], 2) . '</td>';
		echo '<td class="text-center">' . number_format($transfer['paid_thb'], 2) . '</td>';

		echo '<td class="text-right pr-2">' . number_format($payment[0], 2) . '</td>';
		echo '<td class="text-center">';
		echo '<button onclick="fn.app.trust_receipt.tr.dialog_edit(' . $transfer['id'] . ')" class="btn btn-sm btn-info mr-1">Edit</button>';

		echo '<button onclick="fn.app.trust_receipt.tr.dialog_payusd(' . $transfer['id'] . ')" class="btn btn-sm btn-danger mr-1">Pay NonFixed</button>';
		echo '<button onclick="fn.app.trust_receipt.tr.dialog_payment(' . $transfer['id'] . ')" class="btn btn-sm btn-secondary mr-1">Pay Fixed</button>';
		echo '<button onclick="fn.dialog.open(\'apps/forward_contract/view/dialog.contract.lookup.php\',\'#dialog_lookup\',{id:' . $transfer['id'] . '})" class="btn btn-sm btn-info mr-1">View</button>';


		//echo '<button onclick="fn.app.trust_receipt.tr.dialog_interest('.$transfer['id'].')" class="btn btn-sm btn-success mr-1">Interest</button>';
		echo '<button class="btn btn-sm btn-dark mr-1" onclick="fn.app.trust_receipt.usd.dialog_lookup(' . $transfer['id'] . ')">เพิ่ม TR</button>';
		echo '</td>';

		echo '</tr>';
		echo '<tr class="d-none">';
		echo '<td class="pl-5" colspan="17">';
		echo '<table class="table table-sm table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">Date</th>';
		echo '<th class="text-center">Principle THB</th>';
		echo '<th class="text-center">THB Paid</th>';
		echo '<th class="text-center">Interest Rate</th>';
		echo '<th class="text-center">Interest Period</th>';
		echo '<th class="text-center">Interest</th>';
		echo '<th class="text-center">Principle USD</th>';
		echo '<th class="text-center">USD Paid</th>';
		echo '<th class="text-center">Counter Rate</th>';
		echo '<th class="text-center">Interest Paid(USD)</th>';
		echo '<th class="text-center">Action</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		echo '<tr>';
		echo '<td class="text-center" colspan="6">จ่าย ณ วันตั้งหนี้</td>';
		echo '<td class="text-right pr-2">' . number_format($transfer['value_usd_paid'], 2) . '</td>';

		echo '<td class="text-center">ค่าธรรมเนียม</td>';
		echo '<td class="text-right pr-2">' . number_format($transfer['value_thb_transaction'], 2) . '</td>';
		echo '<td class="text-center">-</td>';
		echo '</tr>';
		$sql = "SELECT * FROM bs_transfer_payments WHERE transfer_id=" . $transfer['id'];
		$rst_usd = $dbc->Query($sql);
		while ($payment = $dbc->Fetch($rst_usd)) {
			$btnChange = '<button onclick="fn.app.trust_receipt.payment.dialog_change_date(' . $payment['id'] . ')" class="btn btn-warning btn-xs mr-1">Change Date</button>';

			$btnRemove = '<button onclick="fn.app.trust_receipt.tr.payment_remove(' . $payment['id'] . ')" class="btn btn-danger btn-xs">Remove</button>';

			echo '<tr>';
			echo '<td class="text-center">' . $payment['date'] . '</td>';
			if ($payment['currency'] == "THB") {
				echo '<td class="text-right pr-2">' . number_format($payment['principle'], 2) . '</td>';

				echo '<td class="text-right pr-2">' . number_format($payment['paid'], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['rate_interest'], 2) . '</td>';
				echo '<td class="text-center">' . $payment['date_from'] . ' to ' . $payment['date_to'] . '</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['interest'], 2) . '</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">' . $btnChange . $btnRemove . '</td>';
			} else if ($payment['currency'] == "USD") {
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-center">-</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['principle'], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['paid'], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['rate_counter'], 2) . '</td>';
				echo '<td class="text-right pr-2">' . number_format($payment['interest'], 2) . '</td>';
				echo '<td class="text-center">' . $btnChange . $btnRemove . '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';

		echo '<table class="table table-bordered table-stripe">';
		echo '<thead>';
		echo '<tr>';
		echo '<th class="text-center">Added Date</th>';
		echo '<th class="text-center">ID</th>';
		echo '<th class="text-center">Date</th>';
		echo '<th class="text-center">Preminum Start</th>';
		echo '<th class="text-center">Preminum End</th>';
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


		$sql = "SELECT date FROM bs_transfer_usd WHERE transfer_id = " . $transfer['id'] . " GROUP BY date";
		$rst_transfer_group = $dbc->Query($sql);

		while ($transfer_group  = $dbc->Fetch($rst_transfer_group)) {
			if (!is_null($transfer_group['date'])) {
				$sql = "SELECT * FROM bs_transfer_usd WHERE transfer_id = " . $transfer['id'] . " AND date = '" . $transfer_group['date'] . "'";
				$rst_usd = $dbc->Query($sql);
				$total = array(0, 0, 0, 0, 0);
				while ($item = $dbc->Fetch($rst_usd)) {
					$purchase = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $item['purchase_id']);
					$thb = $purchase['rate_finance'] * $purchase['amount'];

					echo '<tr>';
					$btnChange = '<button onclick="fn.app.trust_receipt.tr.dialog_change_date(' . $item['purchase_id'] . ')" class="btn btn-warning btn-xs mr-1">Change Date</button>';

					$btnRemove = '<button onclick="fn.app.trust_receipt.tr.remove(' . $item['purchase_id'] . ')" class="btn btn-danger btn-xs">Delete TR</button>';

					if ($item['premium_type'] == 1) {
						echo '<td class="text-center">' . $item['date'] . '</td>';
						echo '<td class="text-center">' . $purchase['id'] . '</td>';
						echo '<td class="text-center">' . $purchase['date'] . '</td>';
						echo '<td class="text-center">' . $item['premium_start'] . '</td>';
						echo '<td class="text-center">' . $item['premium_end'] . '</td>';
						echo '<td class="text-center">' . $item['fw_contract_no'] . '</td>';
						echo '<td class="text-right">' . $purchase['rate_finance'] . '</td>';
						echo '<td class="text-right">' . $item['premium'] . '</td>';
						echo '<td class="text-right">' . ($purchase['rate_finance'] + $item['premium']) . '</td>';
						echo '<td class="text-right">' . number_format($purchase['amount'], 2) . '</td>';
						echo '<td class="text-right">' . number_format($thb, 2) . '</td>';
						echo '<td class="text-right">' . number_format($thb + $item['premium'], 2) . '</td>';
						echo '<td class="text-right">' . $btnChange . $btnRemove . '</td>';
					} else {
						echo '<td class="text-center">' . $item['date'] . '</td>';
						echo '<td class="text-center">' . $purchase['id'] . '</td>';
						echo '<td class="text-center">' . $purchase['date'] . '</td>';
						echo '<td class="text-center">-</td>';
						echo '<td class="text-center">-</td>';
						echo '<td class="text-center">' . $item['fw_contract_no'] . '</td>';
						echo '<td class="text-center">' . $purchase['rate_finance'] . '</td>';
						echo '<td class="text-right">' . $item['premium'] . '</td>';
						echo '<td class="text-right">-</td>';
						echo '<td class="text-right">' . number_format($purchase['amount'], 2) . '</td>';
						echo '<td class="text-right">' . number_format($thb, 2) . '</td>';
						echo '<td class="text-right">' . number_format($thb + $item['premium'], 2) . '</td>';
						echo '<td class="text-right">' . $btnChange . $btnRemove . '</td>';
					}



					echo '</tr>';
					$total[0] += $item['premium'];
					if ($item['premium_type'] == 1) {
						$total[1] += ($purchase['rate_finance'] + $item['premium']);
					}
					$total[2] += $purchase['amount'];
					$total[3] += $thb;
					$total[4] += $thb + $item['premium'];
				}
				echo '<tr>';
				echo '<th class="text-right text-white bg-dark" colspan="7">รวม</th>';
				echo '<th class="text-right">' . number_format($total[0], 2) . '</th>';
				echo '<th class="text-right">' . number_format($total[1], 2) . '</th>';
				echo '<th class="text-right">' . number_format($total[2], 2) . '</th>';
				echo '<th class="text-right">' . number_format($total[3], 2) . '</th>';
				echo '<th class="text-right">' . number_format($total[4], 2) . '</th>';
				echo '</tr>';
			}
		}

		echo '</tbody>';
		echo '</table>';
		echo '</td>';
		echo '</tr>';
	}
}
echo '</tbody>';
echo '</table>';

//echo json_encode($data);

$dbc->Close();
