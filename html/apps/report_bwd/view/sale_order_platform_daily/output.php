<?php
session_start();
include_once "../../../../config/define.php";
include_once "../../../../include/db.php";
include_once "../../../../include/oceanos.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

include "../../include/const.php";

$title = $aReportType[$_POST['type']];
$subtitle = "ประจำวัน วันที่ " . $_POST['date'] . " จาก " . $_POST['plat_form'];

?>
<section class="text-center">
	<h3><?php echo $title; ?></h3>
	<p><?php echo $subtitle; ?> </p>
</section>
<table class="table table-sm table-bordered table-striped">
	<thead class="bg-dark">
		<tr>
			<th class="text-center text-white">NO</th>
			<th class="text-center text-white">DATE ADD</th>
			<th class="text-center text-white">ORDER NO.</th>
			<th class="text-center text-white">INVOICE NO.</th>
			<th class="text-center text-white">CUSTOMER</th>
			<th class="text-center text-white">BARS.</th>
			<th class="text-center text-white">BATH / BARS.</th>
			<th class="text-center text-white">DISCOUNT</th>
			<th class="text-center text-white">PRICE NET.</th>
			<th class="text-center text-white">VAT</th>
			<th class="text-center text-white">TOTAL</th>
			<th class="text-center text-white">DELIVERY DATE</th>
			<th class="text-center text-white">SALES</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$aSum = array(0, 0, 0, 0, 0, 0, 0);
		$sql = "SELECT 
			parent.id, parent.code, parent.customer_name, parent.date, parent.delivery_date, 
			parent.sales, parent.user, parent.vat_type, parent.platform,
			bs_deliveries_bwd.billing_id, os_users.display,
			SUM(o.amount) AS amount,
			SUM(o.price) AS price,
			SUM(o.discount) AS discount,
			SUM(o.net) AS net
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
		LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
		LEFT JOIN os_users ON parent.sales = os_users.id
		WHERE DATE(parent.date) = '" . $_POST['date'] . "' 
		  AND parent.platform = '" . $_POST['plat_form'] . "' 
		  AND parent.parent IS NULL 
		  AND o.status > -1
		GROUP BY parent.id, parent.code, parent.customer_name, parent.date, 
		         parent.delivery_date, parent.sales, parent.user, parent.vat_type, 
		         parent.platform, bs_deliveries_bwd.billing_id, os_users.display";

		$rst = $dbc->Query($sql);
		$number = 1;
		while ($order = $dbc->Fetch($rst)) {
			$vat_rate = 0.07;
			$vat_type = isset($order['vat_type']) ? $order['vat_type'] : 0;

			// กำหนดค่า VAT และ CSS class
			if ($vat_type == 0) {
				// ไม่ถอด VAT และแสดงสีเทา
				$price_net = $order['net'];
				$vat_amount = 0;
				$row_class = 'vat-zero';
			} else {
				// ถอด VAT และแสดงสีขาวปกติ
				$price_net = $order['net'] / (1 + $vat_rate);
				$vat_amount = $order['net'] - $price_net;
				$row_class = '';
			}

			echo '<tr class="' . $row_class . '">';
			echo '<td class="text-center">' . $number . '</td>';
			echo '<td class="text-center">' . $order['date'] . '</td>';
			echo '<td class="text-center">' . $order['code'] . '</td>';
			echo '<td class="text-center">' . $order['billing_id'] . '</td>';
			echo '<td class="text-left">' . $order['customer_name'] . '</td>';
			echo '<td class="text-right">' . number_format($order['amount'], 4) . '</td>';
			echo '<td class="text-right">' . number_format($order['price'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['discount'], 2) . '</td>';
			echo '<td class="text-right">' . number_format($price_net, 2) . '</td>';
			echo '<td class="text-right">' . number_format($vat_amount, 2) . '</td>';
			echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
			echo '<td class="text-center">' . $order['delivery_date'] . '</td>';
			echo '<td class="text-center">' . $order['display'] . '</td>';
			echo '</tr>';

			$aSum[0] += 1;
			$aSum[1] += $order['amount'];
			$aSum[2] += $order['price'];
			$aSum[3] += $order['discount'];
			$aSum[4] += $order['net'];
			$aSum[5] += $price_net;
			$aSum[6] += $vat_amount;
			$number++;
		}

		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center" colspan="5">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
			<th class="text-right"><?php echo number_format($aSum[1], 2) ?></th>
			<th class="text-right"><?php echo number_format($aSum[2], 2) ?></th>
			<th class="text-right"><?php echo number_format($aSum[3], 2) ?></th>
			<th class="text-right"><?php echo number_format($aSum[5], 2) ?></th>
			<th class="text-right"><?php echo number_format($aSum[6], 2) ?></th>
			<th class="text-right"><?php echo number_format($aSum[4], 2) ?></th>
			<th class="text-center" colspan="2"></th>
		</tr>
	</tfoot>
</table>

<style>
	.vat-zero {
		background-color: #f8f9fa;
		/* สีเทาอ่อน */
		color: #6c757d;
	}
</style>