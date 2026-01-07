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
				<th class="text-center font-weight-bold" >สินค้า</th>
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
			$aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

			$sql = "SELECT
			DISTINCT(bs_orders_bwd.product_id) AS product_id,
			bs_products_bwd.name AS product_name
			FROM bs_orders_bwd
			LEFT JOIN bs_products_bwd ON bs_orders_bwd.product_id = bs_products_bwd.id
			WHERE DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'
			AND bs_orders_bwd.parent IS NULL 
			AND bs_orders_bwd.status > -1	
			ORDER BY bs_orders_bwd.product_id ASC";

			$rst = $dbc->Query($sql);
			$number = 1;
			while ($set = $dbc->Fetch($rst)) {
				echo '<tr>';
				echo '<td class="text-left font-weight-bold">' . $set['product_name'] . '</td>';
				$sum_in_month = 0;
				for ($n = 1; $n <= $total_day; $n++) {
					$item = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "' AND DATE_FORMAT(bs_orders_bwd.date,'%e') = " . $n . " 
					AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1	AND bs_orders_bwd.product_id = " . $set['product_id']);
					echo '<td class="text-right pr-2">' . number_format($item[0], 2) . '</td>';
					$aSum[$n - 1] += $item[0];
					$sum_in_month +=  $item[0];
				}
				$aSum[$total_day] += $sum_in_month;
				echo '<td class="text-right pr-2">' . number_format($sum_in_month, 2) . '</td>';
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
					echo '<td class="text-right pr-2">' . number_format($aSum[$n - 1], 2) . '</td>';
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