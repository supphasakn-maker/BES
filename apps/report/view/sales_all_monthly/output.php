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
$subtitle = "ยอดขายต่อเดือน Sales " . $sale_group['fullname'] . " " . date("F Y", strtotime($_POST['month']));

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
                <th class="text-center">Sales</th>
                <th class="text-center">Customer</th>
                <th class="text-center">Product</th>
                <?php
                for ($n = 1; $n <= $total_day; $n++) {
                    echo '<th class="text-center">' . $n . '</th>';
                }
                ?>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

            $sql = "SELECT
                    bs_orders.sales AS sales_id,
                    bs_employees.nickname AS sales,
                    bs_orders.customer_id,
                    bs_orders.product_id AS product_id, 
                    bs_customers.name AS name,
                    bs_products.name AS product_name
                    FROM bs_orders
                    LEFT JOIN bs_customers ON bs_customers.id = bs_orders.customer_id
                    LEFT JOIN bs_products ON bs_products.id = bs_orders.product_id
                    LEFT JOIN bs_employees ON bs_employees.id = bs_orders.sales
                    WHERE DATE_FORMAT(bs_orders.date,'%Y-%m') = '" . $_POST['month'] . "'
                    AND bs_orders.parent IS NULL 
                    AND bs_orders.sales = " . $_POST['sale_group'] . "
                    AND bs_orders.status > -1 
                    GROUP BY customer_id 
                    ORDER BY SUM(bs_orders.amount) DESC ";

            $rst = $dbc->Query($sql);
            $number = 1;
            while ($set = $dbc->Fetch($rst)) {
                echo '<tr>';
                echo '<td class="text-center">' . $set['sales'] . '</td>';
                echo '<td class="text-left">' . $set['name'] . '</td>';
                echo '<td class="text-left">' . $set['product_name'] . '</td>';
                $sum_in_month = 0;
                for ($n = 1; $n <= $total_day; $n++) {
                    $item = $dbc->GetRecord("bs_orders", "SUM(amount)", "DATE_FORMAT(bs_orders.date,'%Y-%m') = '" . $_POST['month'] . "' AND DATE_FORMAT(bs_orders.date,'%e') = " . $n . " 
                        AND bs_orders.parent IS NULL AND bs_orders.status > -1	AND bs_orders.sales = " . $set['sales_id'] . " AND bs_orders.customer_id = " . $set['customer_id']);
                    echo '<td class="text-right pr-2">' . number_format($item[0], 4) . '</td>';
                    $aSum[$n - 1] += $item[0];
                    $sum_in_month +=  $item[0];
                }
                $aSum[$total_day] += $sum_in_month;
                echo '<td class="text-right pr-2">' . number_format($sum_in_month, 4) . '</td>';
                $number++;;
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<?php
$dbc->Close();
?>