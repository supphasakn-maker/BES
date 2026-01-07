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
$monthly = $_POST['month'];
$subtitle = "ประจำเดือน " . $monthly;
?>
<h5 class="text-center">รายงานเม็ดเกิน</h5>
<a class="btn btn-xl btn-outline-dark mr-1" href="#apps/schedule/index.php?view=printable_silver_over" target="_blank"><i class="far fa-print"></i></a>
<table class="table table-sm table-bordered table-striped" id="tblDeposit">
    <thead>

        <tr>
            <th class="text-center font-weight-bold">วันที่</th>
            <th class="text-center font-weight-bold">เลขที่เอกสาร</th>
            <th class="text-center font-weight-bold">ประเภท</th>
            <th class="text-center font-weight-bold">รับเข้า</th>
            <th class="text-center font-weight-bold">เบิก</th>
            <th class="text-center font-weight-bold">คงเหลือ</th>
            <th class="text-center font-weight-bold">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $aSum = array(0, 0, 0);
        $aSums = array(0, 0, 0, 0);
        $purchase = array(0, 0, 0);
        $margin_amount = 0;
        $margin_amount1 = 0;
        $sql = "SELECT bs_stock_adjusted_over.id,bs_stock_adjusted_over.product_id ,bs_products.name,bs_stock_adjusted_over.remark,
        bs_stock_adjusted_over.code_no,bs_stock_adjusted_over.amount,bs_stock_adjusted_over.date,bs_stock_adjusted_over.type_id FROM bs_stock_adjusted_over 
        LEFT OUTER JOIN bs_products ON bs_stock_adjusted_over.product_id = bs_products.id";
        $rst = $dbc->Query($sql);
        while ($production = $dbc->Fetch($rst)) {
            $sum1 = $dbc->GetRecord("bs_stock_adjusted_over", "SUM(amount) AS amount", "type_id = 1 AND id = '" . $production['id'] . "'");
            $sum2 = $dbc->GetRecord("bs_stock_adjusted_over", "SUM(amount) AS amount", "type_id = 2 AND id = '" . $production['id'] . "'");
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 1 ";
            $rst_in = $dbc->Query($sql);
            while ($product = $dbc->Fetch($rst_in)) {
                $aSum[0] += 1;
                $aSum[1] += $product['amount'];
                $margin_amount += $product['amount'];
            }
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 2 ";
            $rst_in = $dbc->Query($sql);
            while ($products = $dbc->Fetch($rst_in)) {
                $purchase[0] += 1;
                $purchase[1] += $products['amount'];
                $margin_amount1 += $products['amount'];
            }
            echo '<tr>';
            echo '<td class="text-center">' . $production['date'] . '</td>';
            echo '<td class="text-center">' . $production['code_no'] . '</td>';
            echo '<td class="text-center">' . $production['name'] . '</td>';
            echo '<td class="text-center">';
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 1 ";
            $rst_in = $dbc->Query($sql);
            while ($import = $dbc->Fetch($rst_in)) {
                echo $import['amount'] . '<br />';
            }
            echo '<td class="text-center">';
            $sql = "SELECT * FROM bs_stock_adjusted_over WHERE id = '" . $production['id'] . "' AND type_id = 2 ";
            $rst_in = $dbc->Query($sql);
            while ($import2 = $dbc->Fetch($rst_in)) {
                echo $import2['amount'] . '<br />';
            }
            echo '<td class="text-center">' .  number_format($margin_amount - $margin_amount1, 4) . '</td>';
            echo '<td class="text-left">' . $production['remark'] . '</td>';
            echo '<td>';
            echo '</tr>';
            $aSums[0] += 1;
            $aSums[1] += $sum1["amount"];
            $aSums[2] += $sum2["amount"];
            $aSums[3] += $sum1["amount"] - $sum2["amount"];
        }

        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="3">รวมทั้งหมด <?php echo $aSums[0]; ?> รายการ</th>
            <th class="text-center"><?php echo number_format($aSums[1], 4); ?></th>
            <th class="text-center"><?php echo number_format($aSums[2], 4); ?> </th>
            <th class="text-center"><?php echo number_format($aSums[3], 4); ?></th>
            <th class="text-center"></th>
        </tr>
    </tfoot>
</table>
<script>
    $(function() {
        //Created By: Brij Mohan
        //Website: https://techbrij.com
        function groupTable($rows, startIndex, total) {
            if (total === 0) {
                return;
            }
            var i, currentIndex = startIndex,
                count = 1,
                lst = [];
            var tds = $rows.find('td:eq(' + currentIndex + ')');
            var ctrl = $(tds[0]);
            lst.push($rows[0]);
            for (i = 1; i <= tds.length; i++) {
                if (ctrl.text() == $(tds[i]).text()) {
                    count++;
                    $(tds[i]).addClass('deleted');
                    lst.push($rows[i]);
                } else {
                    if (count > 1) {
                        ctrl.attr('rowspan', count);
                        groupTable($(lst), startIndex + 1, total - 1)
                    }
                    count = 1;
                    lst = [];
                    ctrl = $(tds[i]);
                    lst.push($rows[i]);
                }
            }
        }
        groupTable($('#tblDeposit tr:has(td)'), 0, 3);
        $('#tblDeposit .deleted').remove();
    });
</script>