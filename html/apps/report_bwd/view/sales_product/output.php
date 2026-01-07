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
            <th class="text-center" rowspan="2">วันที่ส่งของ</th>
            <th class="text-center" rowspan="2">รายละเอียด</th>
            <th class="text-center" colspan="12">ยอดขายต่อเดือน</th>
            <th class="text-center" rowspan="2">สรุป</th>
        </tr>
        <tr>
            <?php
            foreach ($aMonth as $month) {
                echo '<td class="text-center">' . $month . '</td>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT
                bs_orders_bwd.delivery_date AS date,
                SUM(bs_orders_bwd.amount) as total,
                bs_orders_bwd.product_type AS product_type,
                bs_products_type.name AS name,
                bs_products_bwd.name AS product_name
                FROM bs_orders_bwd
                LEFT OUTER JOIN bs_deliveries_bwd ON bs_deliveries_bwd.id = bs_orders_bwd.delivery_id
                LEFT OUTER JOIN bs_products_type ON bs_products_type.id = bs_orders_bwd.product_type
                LEFT OUTER JOIN  bs_products_bwd ON bs_products_type.product_id = bs_products_bwd.id
                WHERE YEAR(date) = '" . $year . "'
                AND bs_orders_bwd.parent IS NULL 
                AND bs_orders_bwd.status > -1
                AND bs_orders_bwd.product_id = '" . $product . "'
                GROUP BY product_type               
                ORDER BY SUM(bs_orders_bwd.amount) DESC";
                
        $rst = $dbc->Query($sql);
        $number = 1;
        while ($set = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . $number . '</td>';
            echo '<td class="text-left">' . $set['product_name'] . '</td>';
            echo '<td class="text-left">' . $set['name'] . '</td>';
            for ($m = 0; $m < count($aMonth); $m++) {
                $item = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '".$set['product_type']."' AND YEAR(date) = '" . $_POST['year'] . "' AND bs_orders_bwd.parent IS NULL 
                    AND bs_orders_bwd.status > -1  AND  MONTH(date) = '" . ($m + 1) . "'");
                $aSum[$m] += $item[0];
                echo '<td class="text-right pr-2">' . number_format($item[0], 2) . '</td>';
            }
            $aSum[12] += $set['total'];
            echo '<td class="text-right pr-2">' . number_format($set['total'], 2) . '</td>';
            echo '</tr>';
            $number++;;
        }

        ?>
    </tbody>
    <tfoot>
		<tr>
			<th class="text-center" colspan="3">รวมทั้งหมด</th>
      <?php
	  for($m = 0; $m < count($aMonth); ++$m) {
          echo '<th class="text-right pr-2">'.number_format($aSum[$m],2).'</th>';
        }
        echo '<th class="text-right pr-2">'.number_format($aSum[12],2).'</th>';
      ?>
		</tr>
	</tfoot>
</table>
<?php
$dbc->Close();
?>