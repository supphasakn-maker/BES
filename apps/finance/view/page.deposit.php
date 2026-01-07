<?php
global $dbc;
$today = time();
?>
<div class="btn-area btn-group mb-2">


	<table id="tblDeposit" class="table table-striped table-bordered table-hover table-middle" width="100%">
		<thead>
			<tr>
				<th class="text-center">วันที่</th>
				<th class="text-center">ลูกค้า</th>
				<th class="text-center">ยอดเงินมัดจำ</th>
				<th class="text-center">ยอดมัดจำคงเหลือ</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT bs_payments.date_active, bs_payment_deposits.customer_id, bs_payment_deposits.id, bs_payment_deposits.amount, bs_payments.ref 
		FROM bs_payments
		LEFT OUTER JOIN bs_payment_deposits ON bs_payments.id = bs_payment_deposits.payment_id
		WHERE bs_payment_deposits.status = 1 
		AND bs_payments.ref LIKE '%โอนล่วงหน้า%' 
		AND YEAR(date_active) > 2022 ";

			$rst = $dbc->Query($sql);

			while ($deposit = $dbc->Fetch($rst)) {
				if (empty($deposit['customer_id'])) {
					continue; // ข้ามถ้าไม่มี customer_id
				}

				$customer = $dbc->GetRecord("bs_customers", "*", "id=" . $deposit['customer_id']);

				if (!is_array($customer) || empty($customer)) {
					continue;
				}

				$deposit_used = $dbc->GetRecord(
					"bs_payment_deposit_use",
					"COALESCE(SUM(amount), 0)",
					"deposit_id=" . $deposit['id']
				);

				$used_amount = (is_array($deposit_used) && isset($deposit_used[0])) ? floatval($deposit_used[0]) : 0;
				$deposit_amount = floatval($deposit['amount'] ?? 0);
				$use = $deposit_amount - $used_amount;

				echo '<tr>';
				echo '<td class="text-center">' . htmlspecialchars($deposit['date_active'] ?? '') . '</td>';
				echo '<td>' . htmlspecialchars($customer['name'] ?? 'ไม่พบข้อมูล') . '</td>';
				echo '<td class="text-right">' . number_format($deposit_amount, 2) . '</td>';
				echo '<td class="text-right">' . number_format($use, 4) . '</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>

	<script>
		$(function() {
			function groupTable($rows, startIndex, total) {
				if (total === 0) {
					return;
				}
				var i, currentIndex = startIndex,
					count = 1,
					lst = [];
				var tds = $rows.find('td:eq(' + currentIndex + ')');
				var ctrl = $(tds[0]);
				lst.push($rows[0]);
				for (i = 1; i <= tds.length; i++) {
					if (ctrl.text() == $(tds[i]).text()) {
						count++;
						$(tds[i]).addClass('deleted');
						lst.push($rows[i]);
					} else {
						if (count > 1) {
							ctrl.attr('rowspan', count);
							groupTable($(lst), startIndex + 1, total - 1)
						}
						count = 1;
						lst = [];
						ctrl = $(tds[i]);
						lst.push($rows[i]);
					}
				}
			}
			groupTable($('#tblDeposit tr:has(td)'), 0, 3);
			$('#tblDeposit .deleted').remove();
		});
	</script>