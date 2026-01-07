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
$sale_group = $dbc->GetRecord("bs_employees", "fullname", "id=" . $_POST['sale_group']);
$year = $_POST['year'];
$subtitle = "ยอดขาย Sales " . $sale_group['fullname'] . " ต่อปี  " . $year;
?>
<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
</section>
<div class="overflow-auto">
    <table class="table table-sm table-bordered table-striped overflow-auto">
        <thead>
            <tr>
                <th class="text-center">Sales</th>
                <th class="text-center">Customer</th>
                <th class="text-center">Product</th>
                <?php
                foreach ($aMonth as $month) {
                    echo '<td class="text-center">' . $month . '</td>';
                }
                ?>
                <th class="text-center">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            $sql = "SELECT bs_orders.customer_id ,bs_orders.sales ,bs_customers.name AS name,SUM(bs_orders.amount) AS total ,
            bs_orders.product_id AS product_id, bs_products.name AS product_name, bs_employees.fullname AS fullname
			FROM bs_orders
			LEFT JOIN bs_customers ON bs_customers.id = bs_orders.customer_id
            LEFT JOIN bs_products ON bs_products.id = bs_orders.product_id
            LEFT JOIN bs_employees ON bs_employees.id = bs_orders.sales
			WHERE YEAR(date) = '" . $_POST['year'] . "'
            AND bs_orders.sales = " . $_POST['sale_group'] . "
			AND bs_orders.parent IS NULL 
			AND bs_orders.status > -1
			GROUP BY customer_id
			ORDER BY SUM(bs_orders.amount) DESC ";
            $rst = $dbc->Query($sql);
            $number = 1;
            while ($set = $dbc->Fetch($rst)) {
                echo '<tr>';
                echo '<td class="text-center">' . $set['fullname'] . '</td>';
                echo '<td class="text-left">' . $set['name'] . '</td>';
                echo '<td class="text-left">' . $set['product_name'] . '</td>';
                $sum_year = 0;
                for ($m = 0; $m < count($aMonth); $m++) {
                    $item = $dbc->GetRecord("bs_orders", "SUM(amount)", "YEAR(date) = '" . $_POST['year'] . "' AND DATE_FORMAT(bs_orders.date,'%m') = " . ($m + 1) . " 
                    AND bs_orders.parent IS NULL
                    AND bs_orders.status > -1 AND bs_orders.sales = " . $set['sales'] . " AND bs_orders.customer_id = " . $set['customer_id']);
                    echo '<td class="text-right pr-2">' . number_format($item[0], 4) . '</td>';
                    $aSum[$m] += $item[0];
                    $sum_year +=  $item[0];
                }

                $aSum[count($aMonth)] += $sum_year;
                echo '<td class="text-right pr-2">' . number_format($sum_year, 4) . '</td>';
                $number++;;
                echo '</tr>';
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center" colspan="3">รวม</th>
                <?php
                for ($i = 0; $i < count($aSum); ++$i) {
                    echo '<td class="text-right pr-2">' . number_format($aSum[$i], 4) . '</td>';
                }
                ?>

            </tr>
        </tfoot>
    </table>
</div>