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
$subtitle = "ประจำปี " . $year;
?>
<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
    <h3>แท่งเงิน</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <td class="text-center text-white"></td>
            <td class="text-center text-white font-weight-bold">MONTHLY</td>
            <th class="text-center text-white font-weight-bold">ORDER</th>
            <th class="text-center text-white font-weight-bold">BARS.</th>
            <th class="text-center text-white font-weight-bold">PRICE</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">FEE</th>
            <th class="text-center text-white font-weight-bold">PRICE NET.</th>
            <th class="text-center text-white font-weight-bold">VAT</th>
            <th class="text-center text-white font-weight-bold">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSumBars = array(0, 0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(DISTINCT parent.id) AS net_order,
			MONTH(parent.date) AS month,
			SUM(o.amount) AS amount,
			SUM(o.price) AS price,
			SUM(o.net) AS net,
			SUM(o.discount) AS discount,
            SUM(o.fee) AS fee,
			SUM(CASE WHEN parent.vat_type = 0 THEN o.net ELSE 0 END) AS net_no_vat,
			SUM(CASE WHEN parent.vat_type != 0 OR parent.vat_type IS NULL THEN o.net ELSE 0 END) AS net_with_vat,
			DATE_FORMAT(parent.date,'%Y-%m') AS sql_month
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
		WHERE YEAR(parent.date) = '" . $_POST['year'] . "' 
		  AND parent.parent IS NULL  
		  AND o.status > -1 
		  AND parent.product_id IN (1,2,3)
		GROUP BY MONTH(parent.date)
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            $vat_rate = 0.07;
            $net_no_vat = $order['net_no_vat'];
            $net_with_vat = $order['net_with_vat'];

            $price_net = $net_no_vat + ($net_with_vat / (1 + $vat_rate));
            $vat_amount = $net_with_vat - ($net_with_vat / (1 + $vat_rate));

            echo '<tr class=" text-dark">';
            echo '<td>';
            echo '<button class="btn btn-sm btn-warning" onclick="$(this).parent().parent().next().toggleClass(\'d-none\')">Toggle</button>';
            echo '</td>';
            echo '<td class="text-center">' . $order['sql_month'] . '</td>';
            echo '<td class="text-center">' . $order['net_order'] . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['amount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';

            $aSumBars[0] += $order['net_order'];
            $aSumBars[1] += $order['amount'];
            $aSumBars[2] += $order['price'];
            $aSumBars[3] += $order['discount'];
            $aSumBars[7] += $order['fee'];
            $aSumBars[4] += $order['net'];
            $aSumBars[5] += $price_net;
            $aSumBars[6] += $vat_amount;

            echo '<tr class=" d-none">';
            echo '<td colspan="9">';

        ?>
            <table class="table table-sm table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th class="text-center text-white">DATE ADD</th>
                        <th class="text-center text-white">ORDER NO.</th>
                        <th class="text-center text-white">INVOICE NO.</th>
                        <th class="text-center text-white">CUSTOMER</th>
                        <th class="text-center text-white">BARS.</th>
                        <th class="text-center text-white">BATH / BARS.</th>
                        <th class="text-center text-white">DISCOUNT</th>
                        <th class="text-center text-white">FEE</th>
                        <th class="text-center text-white">PRICE NET.</th>
                        <th class="text-center text-white">VAT</th>
                        <th class="text-center text-white">TOTAL</th>
                        <th class="text-center text-white">DELIVERY DATE</th>
                        <th class="text-center text-white">SALES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                        parent.id, parent.code, parent.customer_name, parent.date, 
                        parent.delivery_date, parent.sales, parent.user, parent.vat_type,
                        bs_deliveries_bwd.billing_id, os_users.display,
                        SUM(o.amount) AS amount,
                        SUM(o.price) AS price,
                        SUM(o.discount) AS discount,
                        SUM(o.fee) AS fee,
                        SUM(o.net) AS net
                    FROM bs_orders_bwd parent
                    LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
                    LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
                    LEFT JOIN os_users ON parent.sales = os_users.id
                    WHERE DATE_FORMAT(parent.date,'%Y-%m')='" . $order['sql_month'] . "' 
                      AND parent.parent IS NULL
                      AND o.status > -1 
                      AND parent.product_id IN (1,2,3)
                    GROUP BY parent.id, parent.code, parent.customer_name, parent.date, 
                             parent.delivery_date, parent.sales, parent.user, parent.vat_type,
                             bs_deliveries_bwd.billing_id, os_users.display";
                    $rst_daily = $dbc->Query($sql);
                    while ($item = $dbc->Fetch($rst_daily)) {
                        $employee_name = $item['display'] ? $item['display'] : "-";

                        $vat_type = isset($item['vat_type']) ? $item['vat_type'] : 0;

                        // กำหนดค่า VAT และ CSS class ตาม vat_type ของ main order
                        if ($vat_type == 0) {
                            // ไม่ถอด VAT และแสดงสีเทา
                            $item_price_net = $item['net'];
                            $item_vat_amount = 0;
                            $row_class = 'vat-zero';
                        } else {
                            // ถอด VAT และแสดงสีขาวปกติ
                            $item_vat_rate = 0.07;
                            $item_price_net = $item['net'] / (1 + $item_vat_rate);
                            $item_vat_amount = $item['net'] - $item_price_net;
                            $row_class = '';
                        }

                        echo '<tr class="' . $row_class . '">';
                        echo '<td class="text-center">' . $item['date'] . '</td>';
                        echo '<td class="text-center">' . $item['code'] . '</td>';
                        echo '<td class="text-center">' . $item['billing_id'] . '</td>';
                        echo '<td class="text-center">' . $item['customer_name'] . '</td>';
                        echo '<td class="text-right">' . number_format($item['amount'], 4) . '</td>';
                        echo '<td class="text-right">' . number_format($item['price'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['discount'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['fee'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item_price_net, 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item_vat_amount, 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['net'], 2) . '</td>';
                        echo '<td class="text-center">' . $item['delivery_date'] . '</td>';
                        echo '<td class="text-center">' . $employee_name . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php

            echo '</td>';
            echo '</tr>';
        }

        ?>
    </tbody>
    <tfoot class="bg-dark">
        <tr>
            <th class="text-center text-white" colspan="3">รวมแท่งเงิน <?php echo $aSumBars[0]; ?> รายการ</th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[1], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[2], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[3], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[7], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[5], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[6], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[4], 2) ?></th>
        </tr>
    </tfoot>
</table>

<section class="text-center">
    <h3>กล่อง</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <td class="text-center text-white"></td>
            <td class="text-center text-white font-weight-bold">MONTHLY</td>
            <th class="text-center text-white font-weight-bold">ORDER</th>
            <th class="text-center text-white font-weight-bold">BARS.</th>
            <th class="text-center text-white font-weight-bold">PRICE</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">FEE</th>
            <th class="text-center text-white font-weight-bold">PRICE NET.</th>
            <th class="text-center text-white font-weight-bold">VAT</th>
            <th class="text-center text-white font-weight-bold">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // สำหรับกล่อง (product_id > 3)
        $aSumBoxes = array(0, 0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(DISTINCT parent.id) AS net_order,
			MONTH(parent.date) AS month,
			SUM(o.amount) AS amount,
			SUM(o.price) AS price,
			SUM(o.net) AS net,
			SUM(o.discount) AS discount,
            SUM(o.fee) AS fee,
			SUM(CASE WHEN parent.vat_type = 0 THEN o.net ELSE 0 END) AS net_no_vat,
			SUM(CASE WHEN parent.vat_type != 0 OR parent.vat_type IS NULL THEN o.net ELSE 0 END) AS net_with_vat,
			DATE_FORMAT(parent.date,'%Y-%m') AS sql_month
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
		WHERE YEAR(parent.date) = '" . $_POST['year'] . "' 
		  AND parent.parent IS NULL  
		  AND o.status > -1 
		  AND parent.product_id > 3
		GROUP BY MONTH(parent.date)
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            $vat_rate = 0.07;
            $net_no_vat = $order['net_no_vat'];
            $net_with_vat = $order['net_with_vat'];

            $price_net = $net_no_vat + ($net_with_vat / (1 + $vat_rate));
            $vat_amount = $net_with_vat - ($net_with_vat / (1 + $vat_rate));

            echo '<tr class=" text-dark">';
            echo '<td>';
            echo '<button class="btn btn-sm btn-warning" onclick="$(this).parent().parent().next().toggleClass(\'d-none\')">Toggle</button>';
            echo '</td>';
            echo '<td class="text-center">' . $order['sql_month'] . '</td>';
            echo '<td class="text-center">' . $order['net_order'] . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['amount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['price'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';

            $aSumBoxes[0] += $order['net_order'];
            $aSumBoxes[1] += $order['amount'];
            $aSumBoxes[2] += $order['price'];
            $aSumBoxes[3] += $order['discount'];
            $aSumBoxes[7] += $order['fee'];
            $aSumBoxes[4] += $order['net'];
            $aSumBoxes[5] += $price_net;
            $aSumBoxes[6] += $vat_amount;

            echo '<tr class=" d-none">';
            echo '<td colspan="9">';

        ?>
            <table class="table table-sm table-bordered table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th class="text-center text-white">DATE ADD</th>
                        <th class="text-center text-white">ORDER NO.</th>
                        <th class="text-center text-white">INVOICE NO.</th>
                        <th class="text-center text-white">CUSTOMER</th>
                        <th class="text-center text-white">BARS.</th>
                        <th class="text-center text-white">BATH / BARS.</th>
                        <th class="text-center text-white">DISCOUNT</th>
                        <th class="text-center text-white">FEE</th>
                        <th class="text-center text-white">PRICE NET.</th>
                        <th class="text-center text-white">VAT</th>
                        <th class="text-center text-white">TOTAL</th>
                        <th class="text-center text-white">DELIVERY DATE</th>
                        <th class="text-center text-white">SALES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                        parent.id, parent.code, parent.customer_name, parent.date, 
                        parent.delivery_date, parent.sales, parent.user, parent.vat_type,
                        bs_deliveries_bwd.billing_id, os_users.display,
                        SUM(o.amount) AS amount,
                        SUM(o.price) AS price,
                        SUM(o.discount) AS discount,
                        SUM(o.fee) AS fee,
                        SUM(o.net) AS net
                    FROM bs_orders_bwd parent
                    LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
                    LEFT JOIN bs_deliveries_bwd ON parent.delivery_id = bs_deliveries_bwd.id
                    LEFT JOIN os_users ON parent.sales = os_users.id
                    WHERE DATE_FORMAT(parent.date,'%Y-%m')='" . $order['sql_month'] . "' 
                      AND parent.parent IS NULL
                      AND o.status > -1 
                      AND parent.product_id > 3
                    GROUP BY parent.id, parent.code, parent.customer_name, parent.date, 
                             parent.delivery_date, parent.sales, parent.user, parent.vat_type,
                             bs_deliveries_bwd.billing_id, os_users.display";
                    $rst_daily = $dbc->Query($sql);
                    while ($item = $dbc->Fetch($rst_daily)) {
                        $employee_name = $item['display'] ? $item['display'] : "-";

                        $vat_type = isset($item['vat_type']) ? $item['vat_type'] : 0;

                        // กำหนดค่า VAT และ CSS class ตาม vat_type ของ main order
                        if ($vat_type == 0) {
                            // ไม่ถอด VAT และแสดงสีเทา
                            $item_price_net = $item['net'];
                            $item_vat_amount = 0;
                            $row_class = 'vat-zero';
                        } else {
                            // ถอด VAT และแสดงสีขาวปกติ
                            $item_vat_rate = 0.07;
                            $item_price_net = $item['net'] / (1 + $item_vat_rate);
                            $item_vat_amount = $item['net'] - $item_price_net;
                            $row_class = '';
                        }

                        echo '<tr class="' . $row_class . '">';
                        echo '<td class="text-center">' . $item['date'] . '</td>';
                        echo '<td class="text-center">' . $item['code'] . '</td>';
                        echo '<td class="text-center">' . $item['billing_id'] . '</td>';
                        echo '<td class="text-center">' . $item['customer_name'] . '</td>';
                        echo '<td class="text-right">' . number_format($item['amount'], 4) . '</td>';
                        echo '<td class="text-right">' . number_format($item['price'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['discount'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['fee'], 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item_price_net, 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item_vat_amount, 2) . '</td>';
                        echo '<td class="text-right">' . number_format($item['net'], 2) . '</td>';
                        echo '<td class="text-center">' . $item['delivery_date'] . '</td>';
                        echo '<td class="text-center">' . $employee_name . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        <?php

            echo '</td>';
            echo '</tr>';
        }

        ?>
    </tbody>
    <tfoot class="bg-dark">
        <tr>
            <th class="text-center text-white" colspan="3">รวมกล่อง <?php echo $aSumBoxes[0]; ?> รายการ</th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[1], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[2], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[3], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[7], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[5], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[6], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[4], 2) ?></th>
        </tr>
    </tfoot>
</table>

<section class="text-center">
    <h3>สรุปรวมประจำปี</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-primary">
        <tr>
            <th class="text-center text-white">ประเภท</th>
            <th class="text-center text-white">จำนวนรายการ</th>
            <th class="text-center text-white">จำนวนแท่ง/กล่อง</th>
            <th class="text-center text-white">ราคาต่อหน่วย</th>
            <th class="text-center text-white">ส่วนลด</th>
            <th class="text-center text-white">ค่าธรรมเนียม</th>
            <th class="text-center text-white">ราคาสุทธิ (ก่อน VAT)</th>
            <th class="text-center text-white">VAT 7%</th>
            <th class="text-center text-white">ยอดรวม</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left"><strong>แท่งเงิน</strong></td>
            <td class="text-center"><?php echo number_format($aSumBars[0]); ?> รายการ</td>
            <td class="text-right"><?php echo number_format($aSumBars[1], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[2], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[3], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[7], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[5], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[6], 2); ?></td>
            <td class="text-right"><strong><?php echo number_format($aSumBars[4], 2); ?></strong></td>
        </tr>
        <tr>
            <td class="text-left"><strong>กล่อง</strong></td>
            <td class="text-center"><?php echo number_format($aSumBoxes[0]); ?> รายการ</td>
            <td class="text-right"><?php echo number_format($aSumBoxes[1], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[2], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[3], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[7], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[5], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[6], 2); ?></td>
            <td class="text-right"><strong><?php echo number_format($aSumBoxes[4], 2); ?></strong></td>
        </tr>
    </tbody>
    <tfoot class="bg-success">
        <tr>
            <th class="text-left text-white">รวมทั้งหมดประจำปี <?php echo $year; ?></th>
            <th class="text-center text-white"><?php echo number_format($aSumBars[0] + $aSumBoxes[0]); ?> รายการ</th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[1] + $aSumBoxes[1], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[2] + $aSumBoxes[2], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[3] + $aSumBoxes[3], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[7] + $aSumBoxes[7], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[5] + $aSumBoxes[5], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[6] + $aSumBoxes[6], 2); ?></th>
            <th class="text-right text-white"><strong><?php echo number_format($aSumBars[4] + $aSumBoxes[4], 2); ?></strong></th>
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