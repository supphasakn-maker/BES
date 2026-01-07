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
$subtitle = "ประดือน " . date("F Y", strtotime($_POST['month']));

$total_day = date("t", strtotime($_POST['month']))
?>
<section class="text-center">
	<h3><?php echo $title; ?></h3>
	<p><?php echo $subtitle; ?> </p>
</section>
<div class="overflow-auto">
	<table class="table table-sm table-bordered table-striped overflow-auto">
		<thead>
			<tr>
				<th class="text-center">ชื่อพนักงานขาย</th>
				<?php
				for ($n = 1; $n <= $total_day; $n++) {
					echo '<th class="text-center">' . $n . '</th>';
				}
				?>
				<th class="text-center">Total</th>
				<th class="text-center">Target</th>
				<th class="text-center">Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

			$sql = "SELECT
			DISTINCT(bs_orders.sales) AS sales_id,
			bs_employees.nickname AS sales
		FROM bs_orders
    LEFT JOIN bs_employees ON bs_orders.sales = bs_employees.id
    WHERE DATE_FORMAT(bs_orders.date,'%Y-%m') = '" . $_POST['month'] . "'
      AND bs_orders.parent IS NULL 
      AND bs_orders.sales IS NOT NULL 
      AND bs_orders.status > -1
			
		";
			$rst = $dbc->Query($sql);
			$number = 1;
			while ($set = $dbc->Fetch($rst)) {
				echo '<tr>';
				echo '<td class="text-center">' . $set['sales'] . '</td>';
				$sum_in_month = 0;
				for ($n = 1; $n <= $total_day; $n++) {
					$item = $dbc->GetRecord("bs_orders", "SUM(amount)", "DATE_FORMAT(bs_orders.date,'%Y-%m') = '" . $_POST['month'] . "' AND DATE_FORMAT(bs_orders.date,'%e') = " . $n . " 
					AND bs_orders.parent IS NULL AND bs_orders.status > -1	AND bs_orders.sales = " . $set['sales_id']);
					$itemss = floor($item[0] * 100) / 100;
					echo '<td class="text-right pr-2">' . number_format($item[0], 4) . '</td>';
					$aSum[$n - 1] += $item[0];
					$sum_in_month +=  $item[0];
				}
				$aSum[$total_day] += $sum_in_month;
				$summ = floor($sum_in_month * 100) / 100;
				echo '<td class="text-right pr-2">' . number_format($summ, 4) . '</td>';
				$number++;;
				echo '</tr>';
			}

			?>
		</tbody>
		<tfoot>
			<tr>
				<th class="text-center">รวม</th>
				<?php
				for ($n = 1; $n <= $total_day; $n++) {
					$n1 = floor($aSum[$n - 1] * 100) / 100;
					echo '<td class="text-right pr-2">' . number_format($n1, 4) . '</td>';
				}
				$aa = floor($aSum[$total_day] * 100) / 100;
				echo '<th class="text-right pr-2">' . number_format($aSum[$total_day], 4) . '</th>';
				?>
				<th class="text-center" colspan="2"></th>
			</tr>
		</tfoot>
	</table>
</div>
<?php
$dbc->Close();
?>