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
$year = $_POST['year'];
$product = $_POST['product'];
$subtitle = "ประจำปี " . $year;
?>
<section class="text-center">
	<h3><?php echo $title; ?></h3>
	<p><?php echo $subtitle; ?> </p>
</section>
<table class="table table-sm table-bordered table-striped">
	<thead>
		<tr>
			<th class="text-center">วันที่ส่งของ</th>
			<th class="text-center">ชื่อบริษัท</th>
			<th class="text-center">จำนวน</th>
			<th class="text-center">Invoice</th>
			<th class="text-center">COA</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$aSum = array(0);
		$sql = "SELECT
			bs_orders.delivery_date AS date,
			bs_customers.name AS customer_name,
			bs_orders.amount AS amount,
			bs_deliveries.billing_id AS billing_id,
			bs_orders.comment AS comment
		FROM bs_orders
    LEFT JOIN bs_customers ON bs_customers.id = bs_orders.customer_id
    LEFT JOIN bs_deliveries ON bs_deliveries.id = bs_orders.delivery_id
    WHERE YEAR(date) = '" . $_POST['year'] . "'
      AND bs_orders.parent IS NULL 
      AND bs_orders.status > -1
			AND bs_orders.product_id = '" . $_POST['product'] . "'
    ORDER BY bs_orders.delivery_date  DESC
		";
		$rst = $dbc->Query($sql);
		$number = 1;
		while ($set = $dbc->Fetch($rst)) {
			echo '<tr>';
			echo '<td class="text-center">' . $set['date'] . '</td>';
			echo '<td class="text-left">' . $set['customer_name'] . '</td>';
			echo '<td class="text-right pr-2">' . number_format($set['amount'], 2) . '</td>';
			echo '<td class="text-left">' . $set['billing_id'] . '</td>';
			echo '<td class="text-left">' . $set['comment'] . '</td>';
			$aSum[0] += $set['amount'];
			$number++;;
		}

		?>
	</tbody>
	<tfoot>
		<tr>
			<th class="text-center" colspan="2">รวมทั้งหมด</th>
			<?php

			echo '<th class="text-right pr-2">' . number_format($aSum[0], 2) . '</th>';
			?>
			<th class="text-center" colspan="2"></th>
		</tr>
	</tfoot>
</table>
<?php
$dbc->Close();
?>