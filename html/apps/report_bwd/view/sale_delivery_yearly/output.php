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
    <h3>แท่งเงิน (Delivery Report)</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <th class="text-center text-white font-weight-bold">MONTH</th>
            <th class="text-center text-white font-weight-bold">DELIVERY ORDERS</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white">FEE</th>
            <th class="text-center text-white font-weight-bold">PRICE NET. (ก่อน VAT)</th>
            <th class="text-center text-white font-weight-bold">VAT 7%</th>
            <th class="text-center text-white font-weight-bold">NET + SHIPPING</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSumBars = array(0, 0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(DISTINCT parent.id) AS total_order,
			MONTH(parent.delivery_date) AS month,
			SUM(o.amount) AS amount,
			SUM(o.price) AS price,
			SUM(o.total) AS total,
			SUM(o.discount) AS discount,
            SUM(o.fee) AS fee,
			SUM(o.net) AS net,
			SUM(CASE WHEN parent.vat_type = 0 THEN o.net ELSE 0 END) AS net_no_vat,
			SUM(CASE WHEN parent.vat_type != 0 OR parent.vat_type IS NULL THEN o.net ELSE 0 END) AS net_with_vat
		FROM bs_orders_bwd parent
		LEFT JOIN bs_orders_bwd o ON o.id = parent.id OR o.parent = parent.id
		WHERE YEAR(parent.delivery_date) = '" . $_POST['year'] . "'
		AND parent.parent IS NULL
		AND o.status > 0 
		AND parent.product_id IN (1,2,3)
		GROUP BY MONTH(parent.delivery_date)
		ORDER BY MONTH(parent.delivery_date)
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            $vat_rate = 0.07;
            $net_no_vat = $order['net_no_vat'];
            $net_with_vat = $order['net_with_vat'];

            $price_net = $net_no_vat + ($net_with_vat / (1 + $vat_rate));
            $vat_amount = $net_with_vat - ($net_with_vat / (1 + $vat_rate));

            $month_name = date('M', mktime(0, 0, 0, $order['month'], 1));

            echo '<tr>';
            echo '<td class="text-center">' . $month_name . ' (' . $order['month'] . ')</td>';
            echo '<td class="text-center">' . $order['total_order'] . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['amount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';

            $aSumBars[0] += $order['total_order'];
            $aSumBars[1] += $order['amount'];
            $aSumBars[2] += $order['total'];
            $aSumBars[3] += $order['discount'];
            $aSumBars[7] += $order['fee'];
            $aSumBars[4] += $order['net'];
            $aSumBars[5] += $price_net;
            $aSumBars[6] += $vat_amount;
        }
        ?>
    </tbody>
    <tfoot class="bg-dark">
        <tr>
            <th class="text-center text-white" colspan="2">รวมแท่งเงิน <?php echo $aSumBars[0]; ?> รายการ</th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[1], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[3], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[7], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[5], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[6], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBars[4], 2) ?></th>
        </tr>
    </tfoot>
</table>

<section class="text-center">
    <h3>กล่อง (Delivery Report)</h3>
</section>
<table class="table table-sm table-bordered table-striped">
    <thead class="bg-dark">
        <tr>
            <th class="text-center text-white font-weight-bold">MONTH</th>
            <th class="text-center text-white font-weight-bold">DELIVERY ORDERS</th>
            <th class="text-center text-white font-weight-bold">BARS</th>
            <th class="text-center text-white font-weight-bold">DISCOUNT</th>
            <th class="text-center text-white font-weight-bold">FEE</th>
            <th class="text-center text-white font-weight-bold">PRICE NET. (ก่อน VAT)</th>
            <th class="text-center text-white font-weight-bold">VAT 7%</th>
            <th class="text-center text-white font-weight-bold">NET + SHIPPING</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // สำหรับกล่อง (product_id > 3)
        $aSumBoxes = array(0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT
			COUNT(id) AS total_order,
			MONTH(delivery_date) AS month,
			SUM(amount) AS amount,
			SUM(price) AS price,
			SUM(total) AS total,
			SUM(discount) AS discount,
            SUM(fee) AS fee,
			SUM(net) AS net,
			SUM(CASE WHEN vat_type = 0 THEN net ELSE 0 END) AS net_no_vat,
			SUM(CASE WHEN vat_type != 0 OR vat_type IS NULL THEN net ELSE 0 END) AS net_with_vat
		FROM bs_orders_bwd 
		WHERE YEAR(delivery_date) = '" . $_POST['year'] . "'
		AND status > 0 
		AND product_id > 3
		GROUP BY MONTH(delivery_date)
		ORDER BY MONTH(delivery_date)
		";
        $rst = $dbc->Query($sql);
        while ($order = $dbc->Fetch($rst)) {

            $vat_rate = 0.07;
            $net_no_vat = $order['net_no_vat'];
            $net_with_vat = $order['net_with_vat'];

            $price_net = $net_no_vat + ($net_with_vat / (1 + $vat_rate));
            $vat_amount = $net_with_vat - ($net_with_vat / (1 + $vat_rate));

            $month_name = date('M', mktime(0, 0, 0, $order['month'], 1));

            echo '<tr>';
            echo '<td class="text-center">' . $month_name . ' (' . $order['month'] . ')</td>';
            echo '<td class="text-center">' . $order['total_order'] . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['amount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['discount'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['fee'], 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($price_net, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($vat_amount, 2) . '</td>';
            echo '<td class="text-right pr-2">' . number_format($order['net'], 2) . '</td>';
            echo '</tr>';

            $aSumBoxes[0] += $order['total_order'];
            $aSumBoxes[1] += $order['amount'];
            $aSumBoxes[2] += $order['total'];
            $aSumBoxes[3] += $order['discount'];
            $aSumBoxes[7] += $order['fee'];
            $aSumBoxes[4] += $order['net'];
            $aSumBoxes[5] += $price_net;
            $aSumBoxes[6] += $vat_amount;
        }
        ?>
    </tbody>
    <tfoot class="bg-dark">
        <tr>
            <th class="text-center text-white" colspan="2">รวมกล่อง <?php echo $aSumBoxes[0]; ?> รายการ</th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[1], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[3], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[7], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[5], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[6], 2) ?></th>
            <th class="text-right pr-2 text-white"><?php echo number_format($aSumBoxes[4], 2) ?></th>
        </tr>
    </tfoot>
</table>

<section class="text-center">
    <h3>สรุปรวม Delivery ประจำปี <?php echo $year; ?></h3>
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
            <td class="text-right"><strong><?php echo number_format($aSumBars[4], 2); ?></strong></td>
        </tr>
        <tr>
            <td class="text-left"><strong>กล่อง</strong></td>
            <td class="text-center"><?php echo number_format($aSumBoxes[0]); ?> รายการ</td>
            <td class="text-right"><?php echo number_format($aSumBoxes[1], 2); ?></td>
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
            <th class="text-right text-white"><?php echo number_format($aSumBars[3] + $aSumBoxes[3], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[7] + $aSumBoxes[7], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[5] + $aSumBoxes[5], 2); ?></th>
            <th class="text-right text-white"><?php echo number_format($aSumBars[6] + $aSumBoxes[6], 2); ?></th>
            <th class="text-right text-white"><strong><?php echo number_format($aSumBars[4] + $aSumBoxes[4], 2); ?></strong></th>
        </tr>
    </tfoot>
</table>