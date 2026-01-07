<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/iface.php";
include_once "../../../include/demo.php";

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
		$demo = new demo;
?>
		<div class="text-center">
			<h3>Lock Report(ที่ยังไม่ได้กำหนดวันจัดส่ง)</h3>
		</div>

		<table id="tblPurchaseLookup" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th class="text-center">ลูกค้า</th>
					<th class="text-center">จำนวนออเดอร์</th>
					<th class="text-right">จำนวนรวม</th>
					<th class="text-right">ยอดรวม</th>
					<th class="text-center">รายละเอียด</th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Group orders by customer
				$sql = "SELECT 
						customer_name,
						COUNT(*) as order_count,
						SUM(amount) as total_amount,
						SUM(net) as total_net,
						GROUP_CONCAT(CONCAT(code, '|', date, '|', amount, '|', price, '|', net, '|', sales) ORDER BY date DESC SEPARATOR '||') as order_details
						FROM bs_orders 
						WHERE delivery_date IS NULL 
						AND date > '2021-12-01' 
						AND status > 0 
						AND id NOT IN (SELECT DISTINCT parent FROM bs_orders WHERE parent IS NOT NULL)
						GROUP BY customer_name 
						ORDER BY customer_name";

				$rst = $dbc->Query($sql);
				$row_index = 0;
				while ($group = $dbc->Fetch($rst)) {
					$row_index++;
					echo '<tr>';
					echo '<td class="text-left"><strong>' . $group['customer_name'] . '</strong></td>';
					echo '<td class="text-center">' . $group['order_count'] . '</td>';
					echo '<td class="text-right">' . number_format($group['total_amount'], 4) . '</td>';
					echo '<td class="text-right">' . number_format($group['total_net'], 2) . '</td>';
					echo '<td class="text-center">';
					echo '<button class="btn btn-sm btn-info" onclick="toggleDetails(' . $row_index . ')">ดูรายละเอียด</button>';
					echo '</td>';
					echo '</tr>';

					// Detail rows (hidden by default)
					echo '<tr id="details_' . $row_index . '" style="display:none;">';
					echo '<td colspan="5">';
					echo '<div class="p-3 bg-light">';
					echo '<table class="table table-sm table-bordered mb-0">';
					echo '<thead>';
					echo '<tr class="bg-secondary text-white">';
					echo '<th class="text-center">วันสั่ง</th>';
					echo '<th class="text-center">Order ID</th>';
					echo '<th class="text-right">จำนวน</th>';
					echo '<th class="text-right">บาท/กิโล</th>';
					echo '<th class="text-right">ยอดรวม</th>';
					echo '<th class="text-center">ผู้ขาย</th>';
					echo '</tr>';
					echo '</thead>';
					echo '<tbody>';

					// Parse and display individual orders
					$orders = explode('||', $group['order_details']);
					foreach ($orders as $order_detail) {
						$parts = explode('|', $order_detail);
						if (count($parts) >= 6) {
							echo '<tr>';
							echo '<td class="text-center">' . $parts[1] . '</td>'; // date
							echo '<td class="text-center">' . $parts[0] . '</td>'; // code
							echo '<td class="text-right">' . number_format($parts[2], 4) . '</td>'; // amount
							echo '<td class="text-right">' . number_format($parts[3], 2) . '</td>'; // price
							echo '<td class="text-right">' . number_format($parts[4], 2) . '</td>'; // net
							echo '<td class="text-center">' . $parts[5] . '</td>'; // sales
							echo '</tr>';
						}
					}

					echo '</tbody>';
					echo '</table>';
					echo '</div>';
					echo '</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>

		<script>
			function toggleDetails(rowIndex) {
				var detailRow = document.getElementById('details_' + rowIndex);
				if (detailRow.style.display === 'none') {
					detailRow.style.display = 'table-row';
				} else {
					detailRow.style.display = 'none';
				}
			}
		</script>
<?php
	}
}

$modal = new myModel($dbc, $os->auth);
$modal->setParam($_POST);
$modal->setExtraClass("modal-xl");
$modal->setModel("dialog_lock_lookup", "Lock Report");
$modal->setButton(array(
	array("close", "btn-secondary", "Dismiss")
));
$modal->EchoInterface();

$dbc->Close();
?>