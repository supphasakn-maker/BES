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
$subtitle = "ประจำเดือน " . date("F Y", strtotime($_POST['month']));

$total_day = date("t", strtotime($_POST['month']));
?>
<section class="text-center">
	<h3><?php echo $title; ?></h3>
	<p><?php echo $subtitle; ?> </p>
</section>
<div class="overflow-auto">
	<table class="table table-sm table-bordered table-striped overflow-auto">
		<thead>
			<tr>
				<th class="text-center font-weight-bold" colspan="2">Platform</th>
				<?php
				for ($n = 1; $n <= $total_day; $n++) {
					echo '<th class="text-center font-weight-bold">' . $n . '</th>';
				}
				?>
				<th class="text-center font-weight-bold">Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			// ปรับปรุง: ใช้ array_fill แทนการกำหนด manual และเพิ่ม 1 element สำหรับ total
			$aSum = array_fill(0, $total_day + 1, 0);
			$aSumNet = array_fill(0, $total_day + 1, 0);

			$sql = "SELECT
			DISTINCT(bs_orders_bwd.product_id) AS product_id,
			bs_orders_bwd.platform AS platform,
            bs_products_bwd.name AS product_name
			FROM bs_orders_bwd
			LEFT JOIN bs_products_bwd ON bs_orders_bwd.product_id = bs_products_bwd.id
			WHERE DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'
			AND bs_orders_bwd.parent IS NULL 
			AND bs_orders_bwd.status > -1	
			ORDER BY bs_orders_bwd.product_id ASC, bs_orders_bwd.platform ASC";

			$rst = $dbc->Query($sql);
			$number = 1;
			while ($set = $dbc->Fetch($rst)) {
				echo '<tr>';
				echo '<td class="text-left font-weight-bold">' . htmlspecialchars($set['product_name']) . '</td>';
				echo '<td class="text-left font-weight-bold">' . htmlspecialchars($set['platform']) . '</td>';
				$sum_in_month = 0;

				for ($n = 1; $n <= $total_day; $n++) {
					$item = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "' AND DATE_FORMAT(bs_orders_bwd.date,'%e') = " . $n . " 
					AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1	AND bs_orders_bwd.product_id = " . $set['product_id'] . " AND bs_orders_bwd.platform = '" . $set['platform'] . "'");

					$daily_amount = $item[0] ? $item[0] : 0; // ป้องกัน null values
					echo '<td class="text-right pr-2">' . number_format($daily_amount, 2) . '</td>';
					$aSum[$n - 1] += $daily_amount;
					$sum_in_month += $daily_amount;
				}

				$aSum[$total_day] += $sum_in_month;
				echo '<td class="text-right pr-2 font-weight-bold">' . number_format($sum_in_month, 2) . '</td>';
				$number++;
				echo '</tr>';
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th class="text-center font-weight-bold" colspan="2">รวมทั้งหมด (แท่ง)</th>
				<?php
				for ($n = 1; $n <= $total_day; $n++) {
					echo '<th class="text-right pr-2">' . number_format($aSum[$n - 1], 2) . '</th>';
				}
				echo '<th class="text-right pr-2">' . number_format($aSum[$total_day], 2) . '</th>';
				?>
			</tr>
		</tfoot>
	</table>
</div>
<?php
$dbc->Close();
?>