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

?>
<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
</section>
<a class="btn btn-xl btn-outline-dark mr-1" href="#apps/report_bwd/index.php?view=printableStock&month=<?php echo $_POST['month']; ?>" target="_blank"><i class="far fa-print"></i></a>
<br><br>
<p>Stock Bowins Design</p>
<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr>
            <th class="text-center font-weight-bold" rowspan="2">ลำดับ<br></th>
            <th class="text-center font-weight-bold" colspan="2" rowspan="2">รายละเอียด</th>
            <th class="text-center font-weight-bold" rowspan="2">ยอดยกมา<br>ต้นเดือน</th>
            <th class="text-center font-weight-bold" rowspan="2">รับเข้า</th>
            <th class="text-center font-weight-bold" rowspan="2">ขาย</th>
            <th class="text-center font-weight-bold" rowspan="2">Influencer</th>
            <th class="text-center font-weight-bold" rowspan="2">ยืม</th>
            <th class="text-center font-weight-bold" rowspan="2">ชำรุดของ<br>อยู่ใน Stock</th>
            <th class="text-center font-weight-bold" rowspan="2">ชำรุด <br>ส่งซ่อม</th>
            <th class="text-center font-weight-bold" rowspan="2">ยอดยกไป <br>ณ สิ้นเดือน</th>
            <th class="text-center font-weight-bold" rowspan="2">Stock คงเหลือ<br>ขายได้จริง</th>
            <th class="text-center font-weight-bold" rowspan="2">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0);
        $sql = "SELECT
                bs_products_type.name AS name,
                bs_products_type.id AS product_type,
                bs_products_bwd.name AS product_name
                FROM bs_products_type
                LEFT OUTER JOIN  bs_products_bwd ON bs_products_type.product_id = bs_products_bwd.id
                WHERE type = 'BWD'
                ORDER BY bs_products_type.product_id ASC";
        $rst = $dbc->Query($sql);
        $number = 1;
        while ($set = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . $number . '</td>';
            echo '<td class="text-left">' . $set['product_name'] . '</td>';
            echo '<td class="text-left">' . $set['name'] . '</td>';

            $lastmonth = date("y-m-31", strtotime("$_POST[month] ,-1 month"));
            $getlast = date("y-m-31", strtotime("$_POST[month]"));

            $a = date("t", strtotime("$lastmonth"));
            $b = date("t", strtotime("$getlast"));
            $bb = date("y-m-$a", strtotime("$_POST[month] ,-1 month"));
            $cc = date("y-m-$b", strtotime("$_POST[month]"));

            $lastdate = strtotime("$cc");
            $previousdate = strtotime("$bb");
            $start_date = strtotime("2023-12-12");

            $stock_bwd = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND submited  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $previousdate - 86400) . "'");
            $sale_bwd = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1  AND bs_orders_bwd.date  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $previousdate - 86400) . "'");

            $total_last_month = $stock_bwd[0] - $sale_bwd[0];
            echo $stock_bwd[0] - $sale_bwd[0];
            echo '<td class="text-right">' . number_format($stock_bwd[0] - $sale_bwd[0], 2) . '</td>';

            $line_stocksilver = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND  DATE_FORMAT(bs_stock_bwd.submited,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($line_stocksilver[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($line_stocksilver[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }




            $line_order  = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL 
            AND bs_orders_bwd.status > -1 AND  DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");

            if ($line_order[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($line_order[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2">-</td>';
            }

            $aaaDataInflu  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '4' AND DATE_FORMAT(bs_stock_adjusted_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($aaaDataInflu[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($aaaDataInflu[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }

            $aaaDataMemo = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '1' AND date  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $lastdate - 86400) . "'");
            if ($aaaDataMemo[0] > 0) {
                echo '<td class="text-right pr-2">' . number_format($aaaDataMemo[0], 2) . '</td>';
            } else {
                echo '<td class="text-right pr-2"></td>';
            }

            $aaaDamStock  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '2'");
            if ($aaaDamStock[0] > 0) {
                echo '<td class="text-right pr-2">' . number_format($aaaDamStock[0], 2) . '</td>';
            } else {
                echo '<td class="text-right pr-2"></td>';
            }

            $aaaDam  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '3' AND DATE_FORMAT(bs_stock_adjusted_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($aaaDam[0] > 0) {
                echo '<td class="text-right pr-2">' . number_format($aaaDam[0], 2) . '</td>';
            } else {
                echo '<td class="text-right pr-2"></td>';
            }
            $stock_bwd_today = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND submited  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $lastdate - 86400) . "'");
            $sale_bwd_today = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1  AND bs_orders_bwd.delivery_date  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $lastdate - 86400) . "'");
            $total_last_day = $total_last_month - $sale_bwd_today[0];
            echo '<td class="text-right">' . number_format($total_last_month - $line_order[0], 2) . '</td>';

            $total_stock = $total_last_month - $line_order[0]   + $line_stocksilver[0] - $aaaDataMemo[0] - $aaaDamStock[0] - $aaaDam[0];

            echo '<td class="text-right">' . number_format($total_stock, 2) . '</td>';

            echo '<td>';
            $sql = "SELECT 
                bs_stock_adjusted_bwd.remark AS remark,
                bs_stock_adjusted_bwd.amount AS amount
            FROM bs_stock_adjusted_bwd
            WHERE  bs_stock_adjusted_bwd.product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '1'";
            $rst_driver = $dbc->Query($sql);
            while ($driver = $dbc->Fetch($rst_driver)) {
                echo $driver['remark'] . ' = ' . number_format($driver['amount'], 0);
            }
            echo '</td>';
            echo '</tr>';

            $number++;;
        }
        ?>
    </tbody>
</table>

<p>Stock Bowins Silver</p>
<table class="table table-sm table-bordered table-striped">
    <thead>
        <tr>
        <tr>
            <th class="text-center font-weight-bold" rowspan="2">ลำดับ<br></th>
            <th class="text-center font-weight-bold" colspan="2" rowspan="2">รายละเอียด</th>
            <th class="text-center font-weight-bold" rowspan="2">ยอดยกมา<br>ต้นเดือน</th>
            <th class="text-center font-weight-bold" rowspan="2">รับเข้า</th>
            <th class="text-center font-weight-bold" rowspan="2">ขาย</th>
            <th class="text-center font-weight-bold" rowspan="2">Influencer</th>
            <th class="text-center font-weight-bold" rowspan="2">ยืม</th>
            <th class="text-center font-weight-bold" rowspan="2">ชำรุดของ<br>อยู่ใน Stock</th>
            <th class="text-center font-weight-bold" rowspan="2">ชำรุด <br>ส่งซ่อม</th>
            <th class="text-center font-weight-bold" rowspan="2">ยอดยกไป <br>ณ สิ้นเดือน</th>
            <th class="text-center font-weight-bold" rowspan="2">Stock คงเหลือ<br>ขายได้จริง</th>
            <th class="text-center font-weight-bold" rowspan="2">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0);
        $sql = "SELECT
                bs_products_type.name AS name,
                bs_products_type.id AS product_type,
                bs_products_bwd.name AS product_name
                FROM bs_products_type
                LEFT OUTER JOIN  bs_products_bwd ON bs_products_type.product_id = bs_products_bwd.id
                WHERE type = 'BWS'
                ORDER BY bs_products_type.product_id ASC";
        $rst = $dbc->Query($sql);
        $number = 1;
        while ($set = $dbc->Fetch($rst)) {
            echo '<tr>';
            echo '<td class="text-center">' . $number . '</td>';
            echo '<td class="text-left">' . $set['product_name'] . '</td>';
            echo '<td class="text-left">' . $set['name'] . '</td>';

            $lastmonth = date("y-m-01", strtotime("$_POST[month] ,-1 month"));
            $getlast = date("y-m-01", strtotime("$_POST[month]"));

            $a = date("t", strtotime("$lastmonth"));
            $b = date("t", strtotime("$getlast"));
            $bb = date("y-m-$a", strtotime("$_POST[month] ,-1 month"));
            $cc = date("y-m-$b", strtotime("$_POST[month]"));

            $lastdate = strtotime("$cc");
            $previousdate = strtotime("$bb");
            $start_date = strtotime("2023-12-31");


            $stock_bwd = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND submited  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $previousdate - 86400) . "'");
            $sale_bwd = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1  AND bs_orders_bwd.delivery_date  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $previousdate - 86400) . "'");
            $total_last_month = $stock_bwd[0] - $sale_bwd[0];
            echo '<td class="text-right">' . number_format($total_last_month, 2) . '</td>';
            $line_stocksilver = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND  DATE_FORMAT(bs_stock_bwd.submited,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($line_stocksilver[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($line_stocksilver[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }

            $line_order  = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL 
            AND bs_orders_bwd.status > -1 AND  DATE_FORMAT(bs_orders_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");

            if ($line_order[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($line_order[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2">-</td>';
            }

            $aaaDataInflu  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '4' AND DATE_FORMAT(bs_stock_adjusted_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($aaaDataInflu[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($aaaDataInflu[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }

            $aaaDataMemo = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '1'");
            if ($aaaDataMemo[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($aaaDataMemo[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }

            $aaaDamStock  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '2'");
            if ($aaaDamStock[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($aaaDamStock[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }

            $aaaDam  = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '3' AND DATE_FORMAT(bs_stock_adjusted_bwd.date,'%Y-%m') = '" . $_POST['month'] . "'");
            if ($aaaDam[0] > 0) {
                echo '<td class="text-center pr-2">' . number_format($aaaDam[0], 2) . '</td>';
            } else {
                echo '<td class="text-center pr-2"></td>';
            }
            $stock_bwd_today = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND submited  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $lastdate - 86400) . "'");
            $sale_bwd_today = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "product_type = '" . $set['product_type'] . "' AND bs_orders_bwd.parent IS NULL AND bs_orders_bwd.status > -1  AND bs_orders_bwd.delivery_date  BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $lastdate - 86400) . "'");

            $total_last_day = $stock_bwd_today[0] - $sale_bwd_today[0];
            echo '<td class="text-right">' . number_format($stock_bwd_today[0] - $sale_bwd_today[0], 2) . '</td>';

            $total_stock = $total_last_month - $line_order[0] - $aaaDataMemo[0] - $aaaDamStock[0] - $aaaDam[0];
            echo '<td class="text-right">' . number_format($total_stock, 2) . '</td>';

            echo '<td>';
            $sql = "SELECT 
                bs_stock_adjusted_bwd.remark AS remark,
                bs_stock_adjusted_bwd.amount AS amount
            FROM bs_stock_adjusted_bwd
            WHERE  bs_stock_adjusted_bwd.product_type = '" . $set['product_type'] . "' AND bs_stock_adjusted_bwd.type_id = '1'";
            $rst_driver = $dbc->Query($sql);
            while ($driver = $dbc->Fetch($rst_driver)) {
                echo $driver['remark'] . ' = ' . number_format($driver['amount'], 0);
            }
            echo '</td>';
            echo '</tr>';

            $number++;;
        }
        ?>
    </tbody>
</table>