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
$subtitle = "ระหว่างวันที่ " . $_POST['date_from'] . " ถึงวันที่ " . $_POST['date_to'];
?>

<section class="text-center">
    <h3><?php echo $title; ?></h3>
    <p><?php echo $subtitle; ?> </p>
</section>
<a class="btn btn-xl btn-outline-dark mr-1" href="#apps/schedule/index.php?view=printableprocessinglosscustom&date_from=<?php echo $_POST['date_from']; ?>&date_to=<?php echo $_POST['date_to']; ?>" target="_blank"><i class="far fa-print"></i></a>
<table class="table table-sm table-bordered table-striped" id="tblDeposit">
    <thead>
        <tr>
            <th rowspan="3" class="text-center font-weight-bold align-middle">No.</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">DATE</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">รอบ</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">ใบขน</th>
            <th colspan="2" rowspan="2" class="text-center font-weight-bold align-middle">Shipper</th>
            <th colspan="1" rowspan="2" class="text-center font-weight-bold align-middle">จำนวน</th>
            <th colspan="5" class="text-center font-weight-bold align-middle">รับ (IN)</th>
            <th colspan="2" class="text-center font-weight-bold align-middle">ออก (OUT)</th>
            <th colspan="2" class="text-center font-weight-bold align-middle">น้ำหนักเข้าเซฟ</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">Factory Stock<br>นน.ออกหลอม<br>ทั้งหมด</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">P/L<br>ต่นน.<br>เข้าหลอม</th>
            <th rowspan="3" class="text-center font-weight-bold align-middle">เบอร์เบ้า</th>
        </tr>

        <tr>
            <th rowspan="2" class="text-center font-weight-bold align-middle">เม็ดเสีย<br>ยกมา</th>
            <th colspan="2" class="text-center font-weight-bold align-middle">แท่งเงิน (Bar)</th>
            <th rowspan="2" class="text-center font-weight-bold align-middle">ส่วนต่าง <br>ขาด/เกิน</th>
            <th rowspan="2" class="text-center font-weight-bold align-middle">Total</th>
            <th colspan="1" class="text-center font-weight-bold align-middle">สินค้าสำเร็จรูป</th>
            <th colspan="1" class="text-center font-weight-bold align-middle">Bars</th>
            <th rowspan="2" class="text-center font-weight-bold align-middle">เม็ดเสีย<br>รอการผลิต</th>
            <th rowspan="2" class="text-center font-weight-bold align-middle">เศษเสีย <br> รอ Refine</th>
        </tr>
        <tr>
            <th class="text-center font-weight-bold align-middle">Supplier/Type</th>
            <th class="text-center font-weight-bold align-middle">Brand</th>
            <th class="text-center font-weight-bold align-middle">แท่ง / ชิ้น</th>
            <th class="text-center font-weight-bold align-middle">ตามใบขน</th>
            <th class="text-center font-weight-bold align-middle">ชั่งจริง / หลอม</th>
            <th class="text-center font-weight-bold align-middle">หลอม</th>
            <th class="text-center font-weight-bold align-middle">Grains<br> Wire</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $date = date("Y-m-d", strtotime("+1 day", strtotime($_POST['date_from'])));
        $date_to = $_POST['date_to'];
        $date_form = $_POST['date_from'];
        $dateto   = strtotime($date_to);
        $dateform   = strtotime($date_form);
        date('Y-m', $dateto);
        date('Y-m', $dateform);
        $line = array(0, 0);
        $sql = "SELECT SUM(weight_expected) AS amount FROM bs_scrap_total WHERE DATE_FORMAT(submited,'%Y-%m') = '" . date('Y-m', $dateform) . "'";
        $rst = $dbc->Query($sql);
        while ($sumscrap = $dbc->Fetch($rst)) {
            $line[0] += $sumscrap['amount'];
        }
        echo '<tr>';
        echo '<td class="text-center" colspan="7"></td>';
        echo '<td class="text-center">' . $line[0] . '</td>';
        echo '<td class="text-center" colspan="11"></td>';
        echo '</tr>';


        $aSum = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $sql = "SELECT * FROM bs_productions 
        WHERE product_id != 5 
        AND PMR = 'BWS' 
        AND id != 1741 
        AND submited IS NOT NULL 
        AND submited BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "'
        AND (product_id != 2 OR submited >= '2025-12-23')";
        $rst = $dbc->Query($sql);
        $number = 1;
        while ($production = $dbc->Fetch($rst)) {
            $d1 = strtotime($production['created']);
            $d2 = date('Y-m-d', $d1);
            $sum = $dbc->GetRecord("bs_scrap_total", "SUM(weight_expected) AS amount", "DATE_FORMAT(submited,'%Y-%m') = '" . date('Y-m', $dateform) . "'");
            $sum_scrap = $dbc->GetRecord("bs_processing_summary", "SUM(weight_expected) AS amount", "DATE_FORMAT(submited,'%Y-%m') = '" . date('Y-m', $dateto) . "'");
            $sum_crucible = $dbc->GetRecord("bs_productions_furnace", "COUNT(id) AS amount", "round = '" . $production['id'] . "'");

            echo '<tr>';
            echo '<td class="text-center">' . $number . '</td>';
            echo '<td>' . $production['submited'] . '</td>';
            echo '<td>' . $production['round'] . '</td>';
            echo '<td>';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        echo $import['import_number'] . '<br />';
                    }
                }
            echo '<td>';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        $sql = "SELECT DISTINCT(bs_products_import.name) FROM bs_incoming_plans 
                        LEFT OUTER JOIN bs_products_import ON bs_incoming_plans.remark = bs_products_import.id
                        WHERE bs_incoming_plans.brand LIKE '%" . $import['import_number'] . "%' ";
                        $rst_in = $dbc->Query($sql);
                        while ($income = $dbc->Fetch($rst_in)) {
                            echo $income['name'] . '<br />';
                        }
                    }
                }
            echo '<td>';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        $sql = "SELECT DISTINCT(import_brand) FROM bs_incoming_plans WHERE brand LIKE '%" . $import['import_number'] . "%' ";
                        $rst_in = $dbc->Query($sql);
                        while ($income = $dbc->Fetch($rst_in)) {
                            echo $income['import_brand'] . '<br />';
                        }
                    }
                }

            echo '<td class="text-center">';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        echo $import['import_bar'] . '<br />';
                    }
                }

            $weight_total = $production['weight_out_safe'] + $production['weight_out_refine'];

            echo '<td class="text-center">' . $production['weight_in_safe'] . '</td>';
            echo '<td class="text-center">';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        echo $import['import_weight_in'] . '<br />';
                    }
                }
            echo '<td class="text-center">';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        echo $import['import_weight_actual'] . '<br />';
                    }
                }
            echo '<td class="text-center">';
            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        echo $import['import_weight_margin'] . '<br />';
                    }
                }
            echo '<td class="text-center">' . $production['weight_in_total'] . '</td>';
            echo '<td class="text-center">' . $production['weight_out_packing'] . '</td>';
            echo '<td class="text-center">';
            $sql = "SELECT * FROM bs_products WHERE id = '" . $production['product_id'] . "' ";
            $rst_in = $dbc->Query($sql);
            while ($product = $dbc->Fetch($rst_in)) {
                echo $product['name'];
            }
            echo '<td class="text-center">' . $production['weight_out_safe'] . '</td>';
            echo '<td class="text-center">' . $production['weight_out_refine'] . '</td>';
            echo '<td class="text-center">' . $production['weight_out_total'] . '</td>';

            echo '<td class="text-center">' . $production['weight_margin'] . '</td>';

            echo '<td class="text-center">';
            $sql = "SELECT bs_productions_furnace.crucible FROM bs_productions_furnace 
            LEFT OUTER JOIN bs_productions_crucible ON bs_productions_furnace.crucible = bs_productions_crucible.id
            WHERE bs_productions_furnace.round = '" . $production['id'] . "' ";
            $rst_in = $dbc->Query($sql);
            while ($crucible = $dbc->Fetch($rst_in)) {
                echo $crucible['crucible'] . '<br />';
            }
            $number++;
            echo '</tr>';

            $aSum[0] += 1;

            if (isset($production['data']))
                if ($production['data'] != null) {
                    $data = json_decode($production['data'], true);
                    foreach ($data['import'] as $import) {
                        $aSum[1] += $import['import_bar'];
                        $aSum[2] += $import['import_weight_in'];
                        $aSum[3] += $import['import_weight_actual'];
                        $aSum[4] += $import['import_weight_margin'];
                    }
                }

            $aSum[5] += $production['weight_in_total'];
            $aSum[6] += $production['weight_out_packing'];
            $aSum[7] += $production['weight_out_safe'];
            $aSum[8] += $production['weight_out_refine'];
            $aSum[11] += $production['weight_out_total'];
            $aSum[12] += $production['weight_margin'];

            $aSum[13] += $production['weight_out_total'];

            $aSum[14] += $sum_crucible['amount'];
            $aSum[15] += $production['weight_in_safe'];
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="6">รวมทั้งหมด <?php echo $aSum[0]; ?> รายการ</th>
            <th class="text-center"> <?php echo number_format($aSum[1], 4); ?> </th>

            <th class="text-center"><?php echo number_format($aSum[15], 4); ?></th>
            <th class="text-center"> <?php echo number_format($aSum[2], 4); ?> </th>
            <th class="text-center"> <?php echo number_format($aSum[3], 4); ?> </th>
            <th class="text-center"> <?php echo number_format($aSum[4], 4); ?> </th>
            <th class="text-center"> <?php echo number_format($aSum[5], 4); ?> </th>
            <th class="text-center"> <?php echo number_format($aSum[6], 4); ?> </th>
            <th class="text-center"></th>
            <th class="text-center"> <?php echo number_format($aSum[7], 4); ?></th>
            <th class="text-center"> <?php echo number_format($aSum[8], 4); ?></th>
            <th class="text-center"> <?php echo number_format($aSum[11], 4); ?> </th>
            <th class="text-center"> <?php echo number_format($aSum[12], 4); ?> </th>
        </tr>
    </tfoot>
</table>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th>จำนวนหลอม แท่ง / เม็ด</th>
                    <th class="text-center"><?php echo number_format($aSum[5], 4); ?></th>
                </tr>
                <tr>
                    <th>จำนวนเบ้าที่ใช้</th>
                    <th class="text-center"><?php echo number_format($aSum[14], 4); ?></th>
                </tr>
            </table>
        </div>
        <div class="col-sm-7">

        </div>
        <div class="col-sm-2">
            <table class="table table-sm table-bordered table-striped">
                <tr>
                    <th>P/L (KG.)</th>
                    <th><?php echo number_format($aSum[11] - $aSum[5], 4); ?> </th>
                </tr>
                <tr>
                    <th>เม็ดเสีย (ยกมา)+ชั่งจริง</th>
                    <th><?php echo number_format($aSum[3] + $sum['amount'], 4); ?></th>
                </tr>
                <tr>
                    <th>Processing loss/ton</th>
                    <th><?php echo number_format(($aSum[11] - $aSum[5]) / ($aSum[3] + $sum['amount']) * 1000, 4); ?></th>
                </tr>
                <tr>
                    <th>เม็ดเสียคงเหลือทั้งหมด</th>
                    <th><?php echo number_format($sum_scrap['amount'], 4); ?></th>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <th class="text-left">รอบ</th>
                    <th class="text-center">เม็ดเสียรอการผลิต</th>
                </thead>
                <tbody>
                    <?php
                    $aSum = array(0, 0);
                    $sql = "SELECT bs_productions.round,bs_scrap_total.weight_expected,bs_scrap_total.pack_name ,bs_scrap_total.submited
                    FROM bs_scrap_total 
                    LEFT OUTER JOIN bs_productions ON bs_scrap_total.production_id = bs_productions.id 
                    WHERE bs_scrap_total.pack_name LIKE '%รอการผลิต%' AND DATE_FORMAT(bs_scrap_total.submited,'%Y-%m') = '" . date('Y-m', $dateto) . "'";
                    $rst = $dbc->Query($sql);
                    $number = 1;
                    while ($scrap = $dbc->Fetch($rst)) {


                        echo '<tr>';
                        echo '<td class="text-left">' . $scrap['round'] . '</td>';
                        echo '<td class="text-center">' . $scrap['weight_expected'] . '</td>';



                        $number++;
                        echo '</tr>';
                        $aSum[0] += 1;
                        $aSum[1] += $scrap['weight_expected'];
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-left">รวมทั้งหมด <?php echo $aSum[0]; ?></th>
                        <th class="text-center"> <?php echo $aSum[1]; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-sm-4">
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <th class="text-left">รอบ</th>
                    <th class="text-center">เศษเสีย รอ Refine</th>
                </thead>
                <tbody>
                    <?php
                    $aSum = array(0, 0);
                    $sql = "SELECT bs_productions.round,bs_scrap_total.weight_expected,bs_scrap_total.pack_name,bs_scrap_total.submited 
                FROM bs_scrap_total 
                LEFT OUTER JOIN bs_productions ON bs_scrap_total.production_id = bs_productions.id 
                WHERE bs_scrap_total.pack_name LIKE '%เม็ดเสียรอการ Refine%' AND DATE_FORMAT(bs_scrap_total.submited,'%Y-%m') = '" . date('Y-m', $dateto) . "'";
                    $rst = $dbc->Query($sql);
                    $number = 1;
                    while ($scrap = $dbc->Fetch($rst)) {


                        echo '<tr>';
                        echo '<td class="text-left">' . $scrap['round'] . '</td>';
                        echo '<td class="text-center">' . $scrap['weight_expected'] . '</td>';



                        $number++;
                        echo '</tr>';
                        $aSum[0] += 1;
                        $aSum[1] += $scrap['weight_expected'];
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-left">รวมทั้งหมด <?php echo $aSum[0]; ?></th>
                        <th class="text-center"> <?php echo $aSum[1]; ?></th>
                    </tr>
                </tfoot>
        </div>
        <div class="col-sm-6">

        </div>
    </div>
</div>