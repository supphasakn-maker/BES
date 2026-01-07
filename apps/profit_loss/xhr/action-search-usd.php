<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/session.php";

@ini_set('display_errors', DEBUG_MODE ? 1 : 0);
date_default_timezone_set(DEFAULT_TIMEZONE);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

// รับค่า month filter ถ้ามี
$month_filter = '';
$month_condition = '';
if (isset($_POST['month']) && $_POST['month'] != '') {
    $month_filter = $_POST['month'];
    $time_filter = strtotime($month_filter . '-01'); // เพิ่ม -01 เพื่อให้เป็น valid date
    $month_condition = " AND tmp.date >= '" . date("Y-m-d", $time_filter) . "'";
}

if ($_POST['type'] == "daily") {
?>
    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">Daily USD Purchase Summary</h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2">Date</th>
                    <th class="text-center" colspan="3">Purchase USD (ทั้งก้อน)</th>
                    <th class="text-center" colspan="3">Purchase USD (ใช้จริง)</th>
                    <th class="text-center" rowspan="2">Net Amount</th>
                    <th class="text-center" rowspan="2">Balance</th>
                </tr>
                <tr>
                    <th class="text-center">Count</th>
                    <th class="text-center">Total Amount</th>
                    <th class="text-center">Total Value</th>
                    <th class="text-center">Count</th>
                    <th class="text-center">Total Amount</th>
                    <th class="text-center">Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT tmp.date
                        FROM (
                        SELECT DATE(date) AS date FROM bs_purchase_usd
                        UNION
                        SELECT DATE(value_date) AS date FROM bs_purchase_usd_profit
                        ) AS tmp
                        WHERE YEAR(tmp.date) > 2024
                        AND tmp.date >= '2025-05-02'
                        $month_condition
                        GROUP BY tmp.date
                        ORDER BY tmp.date ASC;";
                $rst = $dbc->Query($sql);
                $balance = 207973.6700;

                while ($record = $dbc->Fetch($rst)) {
                    $margin = 0;

                    // Purchase USD (ทั้งก้อน)
                    $sql_spot = "SELECT COUNT(*) as count_purchase, SUM(amount) as total_amount, SUM(rate_finance * amount) as total_value
                                FROM bs_purchase_usd
                                WHERE date = '" . $record['date'] . "' 
                                AND (bs_purchase_usd.type LIKE 'Physical') 
                                AND parent IS NULL 
                                AND confirm IS NOT NULL";
                    $rst_spot = $dbc->Query($sql_spot);
                    $row_spot = $dbc->Fetch($rst_spot);
                    $spot_count = (int)($row_spot['count_purchase'] ?? 0);
                    $spot_amount = (float)($row_spot['total_amount'] ?? 0);
                    $spot_value = (float)($row_spot['total_value'] ?? 0);
                    $margin -= $spot_amount;

                    // Purchase USD (ใช้จริง)
                    $sql_profit = "SELECT COUNT(*) as count_purchase, SUM(amount) as total_amount, SUM(rate_finance * amount) as total_value
                                  FROM bs_purchase_usd_profit
                                  WHERE bs_purchase_usd_profit.value_date = '" . $record['date'] . "' 
                                  AND (bs_purchase_usd_profit.type LIKE 'Physical' OR bs_purchase_usd_profit.type LIKE 'MTM') 
                                  AND parent IS NULL 
                                  AND confirm IS NOT NULL";
                    $rst_profit = $dbc->Query($sql_profit);
                    $row_profit = $dbc->Fetch($rst_profit);
                    $profit_count = (int)($row_profit['count_purchase'] ?? 0);
                    $profit_amount = (float)($row_profit['total_amount'] ?? 0);
                    $profit_value = (float)($row_profit['total_value'] ?? 0);
                    $margin += $profit_amount;

                    $balance -= $margin;
                    $margin_color = ($margin < 0) ? 'text-danger' : '';

                    echo '<tr>';
                    echo '<td class="text-center">' . $record['date'] . '</td>';
                    echo '<td class="text-center">' . $spot_count . '</td>';
                    echo '<td class="text-right">' . number_format($spot_amount, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($spot_value, 2) . '</td>';
                    echo '<td class="text-center">' . $profit_count . '</td>';
                    echo '<td class="text-right">' . number_format($profit_amount, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($profit_value, 2) . '</td>';
                    echo '<td class="text-right ' . $margin_color . '">' . number_format($margin, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($balance, 2) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

<?php
} else if ($_POST['type'] == "monthly") {
?>
    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">Monthly USD Purchase Summary</h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2">Month</th>
                    <th class="text-center" colspan="3">Purchase USD (ทั้งก้อน)</th>
                    <th class="text-center" colspan="3">Purchase USD (ใช้จริง)</th>
                    <th class="text-center" rowspan="2">Net Amount</th>
                    <th class="text-center" rowspan="2">Balance</th>
                </tr>
                <tr>
                    <th class="text-center">Count</th>
                    <th class="text-center">Total Amount</th>
                    <th class="text-center">Total Value</th>
                    <th class="text-center">Count</th>
                    <th class="text-center">Total Amount</th>
                    <th class="text-center">Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT DATE_FORMAT(tmp.date, '%Y-%m') as month_date
                        FROM (
                        SELECT DATE(date) AS date FROM bs_purchase_usd
                        UNION
                        SELECT DATE(value_date) AS date FROM bs_purchase_usd_profit
                        ) AS tmp
                        WHERE YEAR(tmp.date) > 2024
                        AND tmp.date >= '2025-05-02'
                        $month_condition
                        GROUP BY DATE_FORMAT(tmp.date, '%Y-%m')
                        ORDER BY month_date ASC;";
                $rst = $dbc->Query($sql);
                $balance = 207973.6700;

                while ($record = $dbc->Fetch($rst)) {
                    $month_date = $record['month_date'];
                    $margin = 0;

                    // Purchase USD (ทั้งก้อน)
                    $sql_spot = "SELECT COUNT(*) as count_purchase, SUM(amount) as total_amount, SUM(rate_finance * amount) as total_value
                                FROM bs_purchase_usd
                                WHERE DATE_FORMAT(date, '%Y-%m') = '$month_date' 
                                AND (bs_purchase_usd.type LIKE 'Physical') 
                                AND parent IS NULL 
                                AND confirm IS NOT NULL";
                    $rst_spot = $dbc->Query($sql_spot);
                    $row_spot = $dbc->Fetch($rst_spot);
                    $spot_count = (int)($row_spot['count_purchase'] ?? 0);
                    $spot_amount = (float)($row_spot['total_amount'] ?? 0);
                    $spot_value = (float)($row_spot['total_value'] ?? 0);
                    $margin -= $spot_amount;

                    // Purchase USD (ใช้จริง)
                    $sql_profit = "SELECT COUNT(*) as count_purchase, SUM(amount) as total_amount, SUM(rate_finance * amount) as total_value
                                  FROM bs_purchase_usd_profit
                                  WHERE DATE_FORMAT(bs_purchase_usd_profit.value_date, '%Y-%m') = '$month_date' 
                                  AND (bs_purchase_usd_profit.type LIKE 'Physical' OR bs_purchase_usd_profit.type LIKE 'MTM') 
                                  AND parent IS NULL 
                                  AND confirm IS NOT NULL";
                    $rst_profit = $dbc->Query($sql_profit);
                    $row_profit = $dbc->Fetch($rst_profit);
                    $profit_count = (int)($row_profit['count_purchase'] ?? 0);
                    $profit_amount = (float)($row_profit['total_amount'] ?? 0);
                    $profit_value = (float)($row_profit['total_value'] ?? 0);
                    $margin += $profit_amount;

                    $balance -= $margin;
                    $margin_color = ($margin < 0) ? 'text-danger' : '';
                    $display_month = date("M Y", strtotime($month_date . '-01'));

                    echo '<tr>';
                    echo '<td class="text-center">' . $display_month . '</td>';
                    echo '<td class="text-center">' . $spot_count . '</td>';
                    echo '<td class="text-right">' . number_format($spot_amount, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($spot_value, 2) . '</td>';
                    echo '<td class="text-center">' . $profit_count . '</td>';
                    echo '<td class="text-right">' . number_format($profit_amount, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($profit_value, 2) . '</td>';
                    echo '<td class="text-right ' . $margin_color . '">' . number_format($margin, 2) . '</td>';
                    echo '<td class="text-right">' . number_format($balance, 2) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

<?php
} else {
?>
    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">USD Purchase Report - Detailed View</h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>
        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center table-dark" colspan="11">Date</th>
                </tr>
                <tr>
                    <th class="text-center" rowspan="2">Date</th>
                    <th class="text-center" colspan="4">Purchase USD (ทั้งก้อน)</th>
                    <th class="text-center" colspan="4">Purchase USD (ใช้จริง)</th>
                    <th class="text-center" colspan="2">Margin</th>
                </tr>
                <tr>
                    <th class="text-center">Bank</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Exchange Rate</th>
                    <th class="text-center">Total</th>

                    <th class="text-center">Bank</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Exchange Rate</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT tmp.date
                        FROM (
                        SELECT DATE(date) AS date FROM bs_purchase_usd
                        UNION
                        SELECT DATE(value_date) AS date FROM bs_purchase_usd_profit
                        ) AS tmp
                        WHERE YEAR(tmp.date) > 2024
                        AND tmp.date >= '2025-05-02'
                        $month_condition
                        GROUP BY tmp.date
                        ORDER BY tmp.date ASC;";
                $rst = $dbc->Query($sql);
                $balance = 207973.6700;

                while ($record = $dbc->Fetch($rst)) {
                    $purchase_spot = array();
                    $purchase = array();
                    $margin = 0;

                    $sql = "SELECT * FROM bs_purchase_usd
                            WHERE date = '" . $record['date'] . "' 
                            AND (bs_purchase_usd.type LIKE 'Physical') 
                            AND parent IS NULL 
                            AND confirm IS NOT NULL";
                    $rst_purchase_spot = $dbc->Query($sql);
                    while ($item = $dbc->Fetch($rst_purchase_spot)) {
                        array_push($purchase_spot, array(
                            $item['bank'],
                            $item['amount'],
                            $item['rate_finance'],
                            $item['rate_finance'] * $item['amount']
                        ));
                        $margin -= $item['amount'];
                    }

                    $sql = "SELECT * FROM bs_purchase_usd_profit
                            WHERE bs_purchase_usd_profit.value_date = '" . $record['date'] . "' 
                            AND (bs_purchase_usd_profit.type LIKE 'Physical' OR bs_purchase_usd_profit.type LIKE 'MTM') 
                            AND parent IS NULL 
                            AND confirm IS NOT NULL";
                    $rst_purchase = $dbc->Query($sql);
                    while ($item = $dbc->Fetch($rst_purchase)) {
                        array_push($purchase, array(
                            $item['bank'],
                            $item['amount'],
                            $item['rate_finance'],
                            $item['rate_finance'] * $item['amount']
                        ));
                        $margin += $item['amount'];
                    }

                    $balance -= $margin;

                    $total_row = 1;
                    if (count($purchase_spot) > $total_row) $total_row = count($purchase_spot);
                    if (count($purchase) > $total_row) $total_row = count($purchase);

                    for ($i = 0; $i < $total_row; $i++) {
                        echo '<tr>';
                        if ($i == 0) {
                            echo '<td class="text-center" rowspan="' . $total_row . '">' . $record['date'] . '</td>';
                        }

                        if (isset($purchase_spot[$i])) {
                            echo '<td class="text-center">' . $purchase_spot[$i][0] . '</td>';
                            echo '<td class="text-right">' . number_format($purchase_spot[$i][1], 2) . '</td>';
                            echo '<td class="text-right">' . number_format($purchase_spot[$i][2], 4) . '</td>';
                            echo '<td class="text-right">' . number_format($purchase_spot[$i][3], 2) . '</td>';
                        } else {
                            echo '<td colspan="4"></td>';
                        }

                        if (isset($purchase[$i])) {
                            echo '<td class="text-center">' . $purchase[$i][0] . '</td>';
                            echo '<td class="text-right">' . number_format($purchase[$i][1], 2) . '</td>';
                            echo '<td class="text-right">' . number_format($purchase[$i][2], 4) . '</td>';
                            echo '<td class="text-right">' . number_format($purchase[$i][3], 2) . '</td>';
                        } else {
                            echo '<td colspan="4"></td>';
                        }

                        if ($i == 0) {
                            echo '<td class="text-right" rowspan="' . $total_row . '">' . number_format($balance, 2) . '</td>';
                        }
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

<?php
}

$dbc->Close();
?>