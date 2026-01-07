<?php
global $dbc;

$start_date = strtotime("2022-05-05");
$today = strtotime("-1 day");
$totaldate = floor((time() - $today) / 86400) + 15;

$aDate = array();
$PID = array(1, 3, 4, 5, 2, 6, 7);
$aBalance = array(1204, 206.6716, 0.009, 1.3003, 42, 0, 0);
$aTotal = array();
$aProduct = array();

for ($i = 0; $i < count($PID); $i++) {
    $product = $dbc->GetRecord("bs_products", "*", "id=" . intval($PID[$i]));
    if ($product) {
        array_push($aProduct, $product);
    } else {
        array_push($aProduct, ['id' => $PID[$i], 'name' => 'Unknown Product']);
    }
}

for ($i = 0; $i < count($PID); $i++) {
    $productId = intval($PID[$i]);
    $startDateStr = date("Y-m-d", $start_date - 86400);
    $todayStr = date("Y-m-d", $today - 86400);

    $line_production = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$productId}");
    $production_amount = isset($line_production[0]) ? floatval($line_production[0]) : 0;
    $aBalance[$i] += $production_amount;

    $line_stocksilver = $dbc->GetRecord("bs_stock_silver", "SUM(weight_actual)", "submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$productId}");
    $stocksilver_amount = isset($line_stocksilver[0]) ? floatval($line_stocksilver[0]) : 0;
    $aBalance[$i] += $stocksilver_amount;


    $line_MR = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_packing)", "submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$productId}");
    $mr_amount = isset($line_MR[0]) ? floatval($line_MR[0]) : 0;
    $aBalance[$i] -= $mr_amount;

    $line_order = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date BETWEEN '{$startDateStr}' AND '{$todayStr}' AND status > 0 AND product_id = {$productId}");
    $order_amount = isset($line_order[0]) ? floatval($line_order[0]) : 0;
    $aBalance[$i] -= $order_amount;

    $sql = "SELECT id FROM bs_stock_adjuest_types WHERE type = 1 AND id != 9 ORDER BY sort_id";
    $rst_deposit_types = $dbc->Query($sql);
    if ($rst_deposit_types) {
        while ($type_item = $dbc->Fetch($rst_deposit_types)) {
            $typeId = intval($type_item['id']);
            $line = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount)", "date BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_id = {$productId} AND type_id = {$typeId}");
            $daily_adjusted_amount = isset($line[0]) ? floatval($line[0]) : 0;
            $aBalance[$i] += $daily_adjusted_amount;
        }
    }
}

$aaData_BF = array_fill(0, count($PID), array());
$aaData_In = array_fill(0, count($PID), array());
$aaData_SB = array_fill(0, count($PID), array());
$aaData_MR = array_fill(0, count($PID), array());
$aaData_Out = array_fill(0, count($PID), array());
$aaData_CF = array_fill(0, count($PID), array());

$aaaDataDeposit = array();
$sql = "SELECT id, name FROM bs_stock_adjuest_types WHERE type = 1 ORDER BY sort_id";
$rst = $dbc->Query($sql);
if ($rst) {
    while ($item = $dbc->Fetch($rst)) {
        array_push($aaaDataDeposit, array(
            array("id" => intval($item['id']), "name" => $item['name']),
            ...array_fill(0, count($PID), array())
        ));
    }
}

$aaaDataDeposit_Luckgems = array();
$sql = "SELECT id, name FROM bs_stock_adjuest_types WHERE type = 3 ORDER BY sort_id";
$rst = $dbc->Query($sql);
if ($rst) {
    while ($item = $dbc->Fetch($rst)) {
        array_push($aaaDataDeposit_Luckgems, array(
            array("id" => intval($item['id']), "name" => $item['name']),
            ...array_fill(0, count($PID), array())
        ));
    }
}

$aaaDataMemo = array();
$sql = "SELECT * FROM bs_stock_adjuest_types WHERE type = 2 ORDER BY sort_id";
$rst = $dbc->Query($sql);
if ($rst) {
    while ($item = $dbc->Fetch($rst)) {
        array_push($aaaDataMemo, array(
            array("id" => intval($item['id']), "name" => $item['name']),
            ...array_fill(0, count($PID), array())
        ));
    }
}

$aaData_MemoSum = array_fill(0, count($PID), array());
$aaData_Total = array_fill(0, count($PID), array());
$aaData_Type12_Amount2 = array_fill(0, count($PID), array());

for ($i = 0; $i <= $totaldate; $i++) {
    $iDate = $today + $i * 86400;
    array_push($aDate, $iDate);

    $totalToday = 0;
    $currentDateStr = date("Y-m-d", $iDate);

    for ($j = 0; $j < count($PID); $j++) {
        $productId = intval($PID[$j]);

        $prevDateStr = date("Y-m-d", $iDate - 86400);
        $line_type12_bf = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount2)", "date <= '{$prevDateStr}' AND product_id = {$productId} AND type_id = 12");
        $type12_amount2_bf = isset($line_type12_bf[0]) ? floatval($line_type12_bf[0]) : 0;

        array_push($aaData_BF[$j], $aBalance[$j] + $type12_amount2_bf);

        $line = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "submited LIKE '{$currentDateStr}' AND product_id = {$productId}");
        $production_in = isset($line[0]) ? floatval($line[0]) : 0;
        array_push($aaData_In[$j], $production_in);
        $aBalance[$j] += $production_in;

        $line = $dbc->GetRecord("bs_stock_silver", "SUM(weight_actual)", "submited LIKE '{$currentDateStr}' AND product_id = {$productId}");
        $stock_silver = isset($line[0]) ? floatval($line[0]) : 0;
        array_push($aaData_SB[$j], $stock_silver);
        $aBalance[$j] += $stock_silver;

        $line = $dbc->GetRecord("bs_productions_pmr", "SUM(weight_out_packing)", "submited LIKE '{$currentDateStr}' AND product_id = {$productId}");
        $mr_out = isset($line[0]) ? floatval($line[0]) : 0;
        array_push($aaData_MR[$j], $mr_out);
        $aBalance[$j] -= $mr_out;


        $line = $dbc->GetRecord("bs_orders", "SUM(amount)", "delivery_date LIKE '{$currentDateStr}' AND status > 0 AND product_id = {$productId}");
        $orders_out = isset($line[0]) ? floatval($line[0]) : 0;
        array_push($aaData_Out[$j], $orders_out);
        $aBalance[$j] -= $orders_out;
        $totalToday += $orders_out;

        foreach ($aaaDataDeposit as $k => $deposit) {
            $typeId = intval($deposit[0]['id']);
            $line = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount)", "date <= '{$currentDateStr}' AND product_id = {$productId} AND type_id = {$typeId}");
            $cumulative_amount = isset($line[0]) ? floatval($line[0]) : 0;
            array_push($aaaDataDeposit[$k][$j + 1], $cumulative_amount);

            if ($typeId != 9) {
                $line = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount)", "date = '{$currentDateStr}' AND product_id = {$productId} AND type_id = {$typeId}");
                $daily_amount = isset($line[0]) ? floatval($line[0]) : 0;
                $aBalance[$j] += $daily_amount;
            }
        }

        foreach ($aaaDataDeposit_Luckgems as $k => $luckgem) {
            $typeId = intval($luckgem[0]['id']);
            $line = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount)", "date <= '{$currentDateStr}' AND product_id = {$productId} AND type_id = {$typeId}");
            $cumulative_amount_amount = isset($line[0]) ? floatval($line[0]) : 0;
            array_push($aaaDataDeposit_Luckgems[$k][$j + 1], $cumulative_amount_amount);
        }

        $line_type12 = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount2)", "date <= '{$currentDateStr}' AND product_id = {$productId} AND type_id = 12");
        $type12_amount2 = isset($line_type12[0]) ? floatval($line_type12[0]) : 0;
        array_push($aaData_Type12_Amount2[$j], $type12_amount2);

        array_push($aaData_CF[$j], $aBalance[$j] + $type12_amount2);

        $buffer = 0;
        foreach ($aaaDataMemo as $k => $memo) {
            $typeId = intval($memo[0]['id']);
            $line = $dbc->GetRecord("bs_stock_adjusted", "SUM(amount)", "date <= '{$currentDateStr}' AND product_id = {$productId} AND type_id = {$typeId}");
            $memo_amount = isset($line[0]) ? floatval($line[0]) : 0;
            array_push($aaaDataMemo[$k][$j + 1], $memo_amount);
            $buffer += $memo_amount;
        }

        array_push($aaData_MemoSum[$j], $buffer);
        array_push($aaData_Total[$j], $aBalance[$j] + $buffer + $type12_amount2);
    }

    array_push($aTotal, $totalToday);
}

function getDayColorStyleHead($date)
{
    $dayColors = [
        'Monday' => 'background-color:#ffe594',
        'Tuesday' => 'background-color:#ffc4e8',
        'Wednesday' => 'background-color:#afe8bb',
        'Thursday' => 'background-color:#fc946b',
        'Friday' => 'background-color:#a6ddff',
        'Saturday' => 'background-color:#deabff',
        'Sunday' => 'background-color:#ef4d4d'
    ];
    $dayName = date("l", $date);
    return isset($dayColors[$dayName]) ? $dayColors[$dayName] : '';
}

function getDayColorStyle($date)
{
    $dayColors = [
        'Monday' => 'background-color:#fff8dc; box-shadow: inset 0 0 0 1px #daa520;',
        'Tuesday' => 'background-color:#ffe4f1; box-shadow: inset 0 0 0 1px #e91e63;',
        'Wednesday' => 'background-color:#e8f5e8; box-shadow: inset 0 0 0 1px #4caf50;',
        'Thursday' => 'background-color:#fff5e6; box-shadow: inset 0 0 0 1px #ff9800;',
        'Friday' => 'background-color:#e3f2fd; box-shadow: inset 0 0 0 1px #2196f3;',
        'Saturday' => 'background-color:#f3e5f5; box-shadow: inset 0 0 0 1px #9c27b0;',
        'Sunday' => 'background-color:#ffebee; box-shadow: inset 0 0 0 1px #f44336;'
    ];
    $dayName = date("l", $date);
    return isset($dayColors[$dayName]) ? $dayColors[$dayName] : '';
}

function getDayColorStyleLight($date)
{
    $dayColors = [
        'Monday' => 'background-color:#fffef7;',
        'Tuesday' => 'background-color:#fef7fc;',
        'Wednesday' => 'background-color:#f8fff8;',
        'Thursday' => 'background-color:#fffcf7;',
        'Friday' => 'background-color:#f7fbff;',
        'Saturday' => 'background-color:#fcf7ff;',
        'Sunday' => 'background-color:#fff7f8;'

    ];
    $dayName = date("l", $date);
    return isset($dayColors[$dayName]) ? $dayColors[$dayName] : '';
}

function getDayBorderColor($date)
{
    $borderColors = [
        'Monday' => '#D4AC00',
        'Tuesday' => '#E91E63',
        'Wednesday' => '#4CAF50',
        'Thursday' => '#FF5722',
        'Friday' => '#2196F3',
        'Saturday' => '#9C27B0',
        'Sunday' => '#F44336'
    ];
    $dayName = date("l", $date);
    return isset($borderColors[$dayName]) ? $borderColors[$dayName] : '#495057';
}
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <p class="text-muted mb-0">ช่วงวันที่: <?php echo date('d/m/Y', $aDate[0]); ?> - <?php echo date('d/m/Y', end($aDate)); ?></p>
            <button class="btn btn-sm btn-outline-secondary mt-2" onclick="$('.hidebyclick').toggle()">
                แสดง/ซ่อน รายละเอียด
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body overflow-auto p-0">
            <table class="table table-bordered table-sm mb-0">
                <tbody>
                    <!-- Header วันที่ -->
                    <tr>
                        <th class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">
                            วันที่
                        </th>
                        <?php
                        foreach ($aDate as $date) {
                            $style = getDayColorStyleHead($date);
                            $borderColor = getDayBorderColor($date);
                            echo '<td class="text-center text-dark font-weight-bold day-border" style="' . $style . '; --border-color: ' . $borderColor . ';" colspan="7">';
                            echo date("l d/m/Y", $date);
                            echo '</td>';
                        }
                        ?>
                    </tr>

                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">สินค้า</td>
                        <?php
                        foreach ($aDate as $adate) {
                            $style = getDayColorStyleLight($adate);
                            $borderColor = getDayBorderColor($adate);
                            $colCount = 0;
                            foreach ($aProduct as $product) {
                                $colCount++;
                                $borderClass = '';
                                if ($colCount === 1) $borderClass .= ' first-col';
                                if ($colCount === count($aProduct)) $borderClass .= ' last-col';

                                echo '<td class="text-center text-dark font-weight-bold' . $borderClass . '" style="' . $style . '; --border-color: ' . $borderColor . ';">';
                                echo htmlspecialchars($product['name']);
                                echo '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">ยอดยกมา</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right font-weight-bold' . $borderClass . '" style="' . $dayStyle . '; --border-color: ' . $borderColor . ';">' . number_format($aaData_BF[$j][$i], 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- เข้า -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">เข้า</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_In[$j][$i];
                                $text_color = $value > 0 ? 'color: #28a745; font-weight: bold;' : '';
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- รับเข้า (แท่ง-อื่นๆ) -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">รับเข้า (แท่ง-อื่นๆ)</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_SB[$j][$i];
                                $text_color = $value > 0 ? 'color: #28a745; font-weight: bold;' : '';
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- Deposit types -->
                    <?php foreach ($aaaDataDeposit as $k => $deposit): ?>
                        <tr>
                            <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">
                                <?php echo htmlspecialchars($deposit[0]['name']); ?>
                            </td>
                            <?php
                            for ($i = 0; $i < count($aDate); $i++) {
                                $dayStyle = getDayColorStyleLight($aDate[$i]);
                                $borderColor = getDayBorderColor($aDate[$i]);
                                for ($j = 0; $j < count($PID); $j++) {
                                    $value = $deposit[$j + 1][$i];
                                    $text_color = $value > 0 ? 'color: #28a745; font-weight: bold;' : '';
                                    $borderClass = '';
                                    if ($j === 0) $borderClass .= ' first-col';
                                    if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                    echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                                }
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Luckgems types -->
                    <?php foreach ($aaaDataDeposit_Luckgems as $k => $luckgem): ?>
                        <tr>
                            <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">
                                <?php echo htmlspecialchars($luckgem[0]['name']); ?>
                            </td>
                            <?php
                            for ($i = 0; $i < count($aDate); $i++) {
                                $dayStyle = getDayColorStyleLight($aDate[$i]);
                                $borderColor = getDayBorderColor($aDate[$i]);
                                for ($j = 0; $j < count($PID); $j++) {
                                    $value = $luckgem[$j + 1][$i];
                                    $text_color = $value > 0 ? 'color: #28a745; font-weight: bold;' : '';
                                    $borderClass = '';
                                    if ($j === 0) $borderClass .= ' first-col';
                                    if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                    echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                                }
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>

                    <!-- PMR (อื่นๆ) -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #dc3545; color: white;">PMR (อื่นๆ)</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_MR[$j][$i];
                                $text_color = $value > 0 ? 'color: #dc3545; font-weight: bold;' : '';
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- ออก -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #dc3545; color: white;">ออก</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_Out[$j][$i];
                                $text_color = $value > 0 ? 'color: #dc3545; font-weight: bold;' : '';
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; ' . $text_color . ' --border-color: ' . $borderColor . ';">' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- ยอดส่งรวม -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #ffc107; color: black;">ยอดส่งรวม</td>
                        <?php
                        for ($i = 0; $i < count($aTotal); $i++) {
                            $dayStyle = getDayColorStyle($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            echo '<td class="text-center day-border font-weight-bold" style="' . $dayStyle . '; --border-color: ' . $borderColor . ';" colspan="7">';
                            echo number_format($aTotal[$i], 4);
                            echo '</td>';
                        }
                        ?>
                    </tr>

                    <!-- ยอดคงเหลือยกไป -->
                    <tr>
                        <td class="sticky-left text-center" style="background-color: #28a745; color: white;">ยอดคงเหลือยกไป</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_CF[$j][$i];
                                if ($value < 0) {
                                    $bg_class = 'style="background-color: #f8d7da; color: #721c24; --border-color: ' . $borderColor . ';"';
                                } else {
                                    $bg_class = 'style="' . $dayStyle . '; --border-color: ' . $borderColor . ';"';
                                }
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right font-weight-bold' . $borderClass . '" ' . $bg_class . '>' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- Memo types (รายละเอียด) -->
                    <?php foreach ($aaaDataMemo as $k => $memo): ?>
                        <tr class="hidebyclick" style="display: none;">
                            <td class="sticky-left text-center" style="background-color: #ffc107; color: black;">
                                <?php echo htmlspecialchars($memo[0]['name']); ?>
                            </td>
                            <?php
                            for ($i = 0; $i < count($aDate); $i++) {
                                $dayStyle = getDayColorStyleLight($aDate[$i]);
                                $borderColor = getDayBorderColor($aDate[$i]);
                                for ($j = 0; $j < count($PID); $j++) {
                                    $borderClass = '';
                                    if ($j === 0) $borderClass .= ' first-col';
                                    if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                    echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; --border-color: ' . $borderColor . ';">' . number_format($memo[$j + 1][$i], 4) . '</td>';
                                }
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>

                    <!-- รวม -->
                    <tr>
                        <td class="sticky-left text-center" style="background-color: #007bff; color: white;">รวม</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; --border-color: ' . $borderColor . ';">' . number_format($aaData_MemoSum[$j][$i], 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- Type 12 Amount2 (รายละเอียด) -->
                    <tr class="hidebyclick" style="display: none;">
                        <td class="sticky-left text-center" style="background-color: #17a2b8; color: white;">Type 12 Amount2</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right' . $borderClass . '" style="' . $dayStyle . '; --border-color: ' . $borderColor . ';">' . number_format($aaData_Type12_Amount2[$j][$i], 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>

                    <!-- สินค้าคงคลัง -->
                    <tr>
                        <td class="sticky-left text-center font-weight-bold" style="background-color: #343a40; color: white;">สินค้าคงคลัง</td>
                        <?php
                        for ($i = 0; $i < count($aDate); $i++) {
                            $dayStyle = getDayColorStyleLight($aDate[$i]);
                            $borderColor = getDayBorderColor($aDate[$i]);
                            for ($j = 0; $j < count($PID); $j++) {
                                $value = $aaData_Total[$j][$i];
                                if ($value < 0) {
                                    $bg_class = 'style="background-color: #f8d7da; color: #721c24; font-weight: bold; --border-color: ' . $borderColor . ';"';
                                } else {
                                    $bg_class = 'style="' . $dayStyle . '; --border-color: ' . $borderColor . ';"';
                                }
                                $borderClass = '';
                                if ($j === 0) $borderClass .= ' first-col';
                                if ($j === count($PID) - 1) $borderClass .= ' last-col';

                                echo '<td class="text-right font-weight-bold' . $borderClass . '" ' . $bg_class . '>' . number_format($value, 4) . '</td>';
                            }
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table td,
    .table th {
        border: 1px solid #dee2e6;
        padding: 6px;
        font-size: 0.85rem;
        vertical-align: middle;
    }

    .sticky-left {
        position: sticky;
        left: 0;
        z-index: 10;
        min-width: 150px;
    }

    .table {
        margin-bottom: 0;
    }

    .hidebyclick {
        display: none;
    }


    .first-col {
        border-left: 3px solid var(--border-color, #495057) !important;
    }

    .last-col {
        border-right: 3px solid var(--border-color, #495057) !important;
    }


    .day-border {
        border-left: 3px solid var(--border-color, #495057) !important;
        border-right: 3px solid var(--border-color, #495057) !important;
    }


    /* .table tbody tr:hover td {
		background-color: rgba(0, 123, 255, 0.05) !important;
	} */

    @media (max-width: 768px) {
        .table {
            font-size: 0.7rem;
        }

        .table td,
        .table th {
            padding: 4px;
        }

        .first-col {
            border-left: 2px solid #495057 !important;
        }

        .last-col {
            border-right: 2px solid #495057 !important;
        }
    }
</style>