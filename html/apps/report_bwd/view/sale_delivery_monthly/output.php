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

$net_day = date("t", strtotime($_POST['month']));
?>
<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
    <h3>แท่งเงิน (Delivery Report)</h3>
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
        // สำหรับแท่งเงิน (product_id IN 1,2,3)
        $aSumBars = array(0, 0, 0, 0, 0, 0, 0, 0); // เพิ่มช่องสำหรับค่าธรรมเนียม
        $sql = "
        SELECT 
        parent.id, parent.code, parent.customer_name, parent.date, parent.delivery_date, 
        parent.sales, parent.user, parent.vat_type,
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
        WHERE DATE_FORMAT(parent.delivery_date,'%Y-%m') = '" . $_POST['month'] . "' 
        AND parent.parent IS NULL
        AND o.status > -1
        AND parent.product_id IN (1,2,3)
        GROUP BY parent.id
        ";


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
            echo '<td class="text-right">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $order['delivery_date'] . '</td>';
            echo '<td class="text-center">' . $order['display'] . '</td>';
            echo '</tr>';
            echo '</tr>';
            $aSumBars[0] += 1;
            $aSumBars[1] += $order['amount'];
            $aSumBars[2] += $order['price'];
            $aSumBars[3] += $order['discount'];
            $aSumBars[7] += $order['fee'];
            $aSumBars[4] += $order['net'];
            $aSumBars[5] += $price_net;
            $aSumBars[6] += $vat_amount;
            $number++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="5">รวมแท่งเงิน <?php echo $aSumBars[0]; ?> รายการ</th>
            <th class="text-right"><?php echo number_format($aSumBars[1], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[2], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[3], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[7], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[5], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[6], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBars[4], 2); ?></th>
            <th class="text-center" colspan="2"></th>
        </tr>
    </tfoot>
</table>
<section class="text-center">
    <h3>กล่อง</h3>
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
        $aSumBoxes = array(0, 0, 0, 0, 0, 0, 0, 0); // เพิ่มช่องสำหรับค่าธรรมเนียม

        $sql = "
    SELECT 
        parent.id, parent.code, parent.customer_name, parent.date, parent.delivery_date, 
        parent.sales, parent.user, parent.vat_type,
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
    WHERE DATE_FORMAT(parent.delivery_date,'%Y-%m') = '" . $_POST['month'] . "' 
      AND parent.parent IS NULL
      AND o.status > -1
      AND parent.product_id > 3
    GROUP BY parent.id
";

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
            echo '<td class="text-right">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right">' . number_format($order['net'], 2) . '</td>';
            echo '<td class="text-center">' . $order['delivery_date'] . '</td>';
            echo '<td class="text-center">' . $order['display'] . '</td>';
            echo '</tr>';

            $aSumBoxes[0] += 1;
            $aSumBoxes[1] += $order['amount'];
            $aSumBoxes[2] += $order['price'];
            $aSumBoxes[3] += $order['discount'];
            $aSumBoxes[7] += $order['fee'];
            $aSumBoxes[4] += $order['net'];
            $aSumBoxes[5] += $price_net;
            $aSumBoxes[6] += $vat_amount;
            $number++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="5">รวมกล่อง <?php echo $aSumBoxes[0]; ?> รายการ</th>
            <th class="text-right"><?php echo number_format($aSumBoxes[1], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[2], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[3], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[7], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[5], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[6], 2); ?></th>
            <th class="text-right"><?php echo number_format($aSumBoxes[4], 2); ?></th>
            <th class="text-center" colspan="2"></th>
        </tr>
    </tfoot>
</table>


<section class="text-center">
    <h3>สรุปรวม Delivery ประจำเดือน</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-primary">
        <tr>
            <th class="text-center text-white">ประเภท</th>
            <th class="text-center text-white">จำนวนรายการส่ง</th>
            <th class="text-center text-white">จำนวนแท่ง/กล่อง</th>
            <th class="text-center text-white">ส่วนลด</th>
            <th class="text-center text-white">ค่าธรรมเนียม</th>
            <th class="text-center text-white">ราคาสุทธิ (ก่อน VAT)</th>
            <th class="text-center text-white">VAT 7%</th>
            <th class="text-center text-white">NET + SHIPPING</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left"><strong>แท่งเงิน</strong></td>
            <td class="text-center"><?php echo number_format($aSumBars[0]); ?> รายการ</td>
            <td class="text-right"><?php echo number_format($aSumBars[1], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[3], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[7], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[5], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBars[6], 2); ?></td>
            <td class="text-right"><strong><?php echo number_format($aSumBars[2], 2); ?></strong></td>
        </tr>
        <tr>
            <td class="text-left"><strong>กล่อง</strong></td>
            <td class="text-center"><?php echo number_format($aSumBoxes[0]); ?> รายการ</td>
            <td class="text-right"><?php echo number_format($aSumBoxes[1], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[3], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[7], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[5], 2); ?></td>
            <td class="text-right"><?php echo number_format($aSumBoxes[6], 2); ?></td>
            <td class="text-right"><strong><?php echo number_format($aSumBoxes[2], 2); ?></strong></td>
        </tr>
    </tbody>
    <tfoot class="bg-success">
        <tr>
            <th class="text-left text-white">รวมทั้งหมด (Delivery)</th>
            <th class="text-center text-white"><?php echo number_format($aSumBars[0] + $aSumBoxes[0]); ?> รายการ</th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[1] + $aSumBoxes[1], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[3] + $aSumBoxes[3], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[7] + $aSumBoxes[7], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[5] + $aSumBoxes[5], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[6] + $aSumBoxes[6], 2); ?></th>
            <th class="text-right text-white"><strong><?php echo number_format($aSumBars[2] + $aSumBoxes[2], 2); ?></strong></th>
        </tr>
    </tfoot>
</table>