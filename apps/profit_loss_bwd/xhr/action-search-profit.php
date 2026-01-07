<?php
session_start();
include_once "../../../config/define.php";
include_once "../../../include/db.php";
include_once "../../../include/oceanos.php";
include_once "../../../include/session.php";

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
error_reporting(E_ALL);

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);

$month_filter = '';
$month_condition = '';
if (isset($_POST['month']) && $_POST['month'] != '') {
    $month_filter = $_POST['month'];
    $time_filter = strtotime($month_filter . '-01');
    $month_condition = " AND mapped >= '" . date("Y-m-d", $time_filter) . "'";
}

function getCheckboxStatus($dbc, $report_date)
{
    $sql_usd = "SELECT is_checked FROM bs_mapping_profit_sumusd_bwd 
                WHERE DATE(mapped) = '$report_date' AND is_checked = 1 
                LIMIT 1";
    $result_usd = $dbc->Query($sql_usd);
    if ($result_usd && $dbc->Fetch($result_usd)) {
        return true;
    }

    $sql_thb = "SELECT is_checked FROM bs_mapping_profit_bwd 
                WHERE DATE(mapped) = '$report_date' AND is_checked = 1 
                LIMIT 1";
    $result_thb = $dbc->Query($sql_thb);
    if ($result_thb && $dbc->Fetch($result_thb)) {
        return true;
    }

    return false;
}

function getMonthlyCheckboxStatus($dbc, $report_month)
{
    $sql_usd = "SELECT is_checked FROM bs_mapping_profit_sumusd_bwd
                WHERE DATE_FORMAT(mapped, '%Y-%m') = '$report_month' AND is_checked = 1 
                LIMIT 1";
    $result_usd = $dbc->Query($sql_usd);
    if ($result_usd && $dbc->Fetch($result_usd)) {
        return true;
    }

    $sql_thb = "SELECT is_checked FROM bs_mapping_profit_bwd 
                WHERE DATE_FORMAT(mapped, '%Y-%m') = '$report_month' AND is_checked = 1 
                LIMIT 1";
    $result_thb = $dbc->Query($sql_thb);
    if ($result_thb && $dbc->Fetch($result_thb)) {
        return true;
    }

    return false;
}
?>
<style>
    .row-checked {
        background-color: #f8f9fa !important;
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .row-checked:hover {
        background-color: #e9ecef !important;
        opacity: 0.8;
    }

    .checkbox-cell {
        width: 50px;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    .checkbox-cell input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #007bff;
        transform: scale(1.2);
    }

    .checkbox-cell input[type="checkbox"]:hover {
        transform: scale(1.3);
        transition: transform 0.2s ease;
    }

    .checkbox-loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .checkbox-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid transparent;
        border-top-color: #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .bulk-actions {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }

    .bulk-actions button {
        margin-right: 10px;
        margin-bottom: 5px;
    }

    .checkbox-success {
        animation: successPulse 0.6s ease;
    }

    .checkbox-error {
        animation: errorShake 0.6s ease;
    }

    @keyframes successPulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
            background-color: #28a745;
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes errorShake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    @media (max-width: 768px) {
        .checkbox-cell {
            width: 40px;
        }

        .checkbox-cell input[type="checkbox"] {
            width: 16px;
            height: 16px;
            transform: scale(1);
        }
    }
</style>
<script>
    window.checkboxData = window.checkboxData || {};

    function toggleRowCheck(checkbox, reportDate) {
        const row = checkbox.closest('tr');
        const isChecked = checkbox.checked;

        if (isChecked) {
            row.classList.add('row-checked');
        } else {
            row.classList.remove('row-checked');
        }

        checkbox.disabled = true;
        const cell = checkbox.closest('.checkbox-cell');
        if (cell) cell.classList.add('checkbox-loading');

        const reportType = window.checkboxData.currentReportType || 'daily';

        fetch('apps/profit_loss_bwd/xhr/update_checkbox_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `report_date=${reportDate}&is_checked=${isChecked ? 1 : 0}&report_type=${reportType}`
            })
            .then(response => response.json())
            .then(data => {
                checkbox.disabled = false;
                if (cell) cell.classList.remove('checkbox-loading');

                if (data.success) {
                    showNotification('Status updated successfully!', 'success');
                    updateCounter();
                } else {
                    checkbox.checked = !isChecked;
                    if (!isChecked) {
                        row.classList.add('row-checked');
                    } else {
                        row.classList.remove('row-checked');
                    }
                    showNotification('Error: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                checkbox.disabled = false;
                if (cell) cell.classList.remove('checkbox-loading');

                checkbox.checked = !isChecked;
                if (!isChecked) {
                    row.classList.add('row-checked');
                } else {
                    row.classList.remove('row-checked');
                }
                showNotification('Network error: ' + error.message, 'error');
            });
    }

    function checkAll() {
        console.log('checkAll called');
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:not([disabled])');
        console.log('Found checkboxes:', checkboxes.length);

        if (checkboxes.length === 0) {
            showNotification('No checkboxes found', 'warning');
            return;
        }

        showNotification(`Checking ${checkboxes.length} items...`, 'info');

        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.checked = true;
                const row = checkbox.closest('tr');
                row.classList.add('row-checked');

                const reportDate = checkbox.getAttribute('data-date');
                if (reportDate) {
                    updateCheckboxServer(reportDate, true);
                }
            }
        });

        updateCounter();
        setTimeout(() => {
            showNotification('All items checked!', 'success');
        }, 500);
    }

    function uncheckAll() {
        console.log('uncheckAll called');
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:not([disabled])');
        console.log('Found checkboxes:', checkboxes.length);

        if (checkboxes.length === 0) {
            showNotification('No checkboxes found', 'warning');
            return;
        }

        showNotification(`Unchecking ${checkboxes.length} items...`, 'info');

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
                const row = checkbox.closest('tr');
                row.classList.remove('row-checked');

                const reportDate = checkbox.getAttribute('data-date');
                if (reportDate) {
                    updateCheckboxServer(reportDate, false);
                }
            }
        });

        updateCounter();
        setTimeout(() => {
            showNotification('All items unchecked!', 'success');
        }, 500);
    }

    function updateCheckboxServer(reportDate, isChecked) {
        const reportType = window.checkboxData.currentReportType || 'daily';

        fetch('apps/profit_loss_bwd/xhr/update_checkbox_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `report_date=${reportDate}&is_checked=${isChecked ? 1 : 0}&report_type=${reportType}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to update server:', data.message);
                }
            })
            .catch(error => {
                console.error('Server update error:', error);
            });
    }

    function updateCounter() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:not([disabled])');
        const checkedCount = document.querySelectorAll('tbody input[type="checkbox"]:checked:not([disabled])').length;
        const totalCount = checkboxes.length;

        const counterElement = document.getElementById('checkbox-counter');
        if (counterElement) {
            counterElement.textContent = `${checkedCount} of ${totalCount} checked`;
        }
    }

    function showNotification(message, type = 'info') {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            info: '#17a2b8',
            warning: '#ffc107'
        };

        let notification = document.getElementById('checkbox-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'checkbox-notification';
            notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            max-width: 350px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
            document.body.appendChild(notification);
        }

        notification.textContent = message;
        notification.style.backgroundColor = colors[type] || colors.info;

        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing checkbox system');
        updateCounter();

        if (document.querySelector('h4').textContent.includes('Monthly')) {
            window.checkboxData.currentReportType = 'monthly';
        } else {
            window.checkboxData.currentReportType = 'daily';
        }

        console.log('Report type set to:', window.checkboxData.currentReportType);
    });

    setTimeout(function() {
        updateCounter();
    }, 1000);
</script>
<?php
if ($_POST['type'] == "daily") {
?>
    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">BWD Daily Profit Summary </h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <button type="button" class="btn btn-sm btn-primary" onclick="checkAll()">
                <i class="fas fa-check-square"></i> Check All
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="uncheckAll()">
                <i class="far fa-square"></i> Uncheck All
            </button>
            <span class="ml-3 text-muted">
                Status: <span id="checkbox-counter">0 of 0 checked</span>
            </span>
        </div>

        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center table-dark" colspan="17">BWD Daily Report</th>
                </tr>
                <tr>
                    <th class="text-center checkbox-cell" rowspan="2">✓</th>
                    <th class="text-center" rowspan="2">Date</th>
                    <th class="text-center" colspan="3">Sales</th>
                    <th class="text-center" colspan="4">Purchase Spot</th>
                    <th class="text-center" colspan="3">Purchase USD</th>
                    <th class="text-center" colspan="4">Margin</th>
                    <th class="text-center" rowspan="2">Profit(THB)</th>
                </tr>
                <tr>
                    <th class="text-center">Total Order</th>
                    <th class="text-center">Amount (KG)</th>
                    <th class="text-center">Total Value (THB)</th>

                    <th class="text-center">Total Purchase</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">USD Match</th>
                    <th class="text-center">THB Match</th>

                    <th class="text-center">Purchase</th>
                    <th class="text-center">Used USD</th>
                    <th class="text-center">THB (Purchase USD)</th>

                    <th class="text-center">Amount Margin</th>
                    <th class="text-center">Amount Balance</th>
                    <th class="text-center">USD Margin</th>
                    <th class="text-center">USD Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_dates = "SELECT DISTINCT DATE(mapped) AS report_date
                              FROM bs_mapping_profit_sumusd_bwd
                              WHERE 1=1 $month_condition
                              UNION
                              SELECT DISTINCT DATE(mapped) AS report_date
                              FROM bs_mapping_profit_bwd
                              WHERE 1=1 $month_condition
                              ORDER BY report_date ASC";
                $rst_dates = $dbc->Query($sql_dates);

                $total_all_profit = 0;
                $balance_amount = 0;
                $balance_usd = 0;

                if ($rst_dates) {
                    while ($row_date = $dbc->Fetch($rst_dates)) {
                        $report_date = $row_date['report_date'];
                        $date_filter = $report_date;

                        $is_checked = getCheckboxStatus($dbc, $report_date);
                        $row_class = $is_checked ? 'row-checked' : '';
                        echo '<tr class="' . $row_class . '">';
                        echo '<td class="text-center checkbox-cell">';
                        echo '<input type="checkbox" data-date="' . $report_date . '" ' . ($is_checked ? 'checked' : '') . ' onchange="toggleRowCheck(this, \'' . $report_date . '\')">';
                        echo '</td>';
                        echo '<td class="text-center">' . $report_date . '</td>';

                        // Initialize variables
                        $order_count = 0;
                        $order_amount = 0;
                        $order_value = 0;

                        $purchase_count = 0;
                        $purchase_amount = 0;
                        $purchase_usd_value = 0;
                        $purchase_thb_value = 0;

                        $usd_purchase_count = 0;
                        $usd_purchase_amount = 0;
                        $usd_purchase_thb_value = 0;

                        $margin_amount = 0;
                        $margin_usd = 0;

                        // Sales data
                        $sql_usd = "SELECT COUNT(bs_mapping_profit_orders_usd_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_usd_bwd.total) AS totalordersusd
                                    FROM `bs_mapping_profit_sumusd_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                        ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                    WHERE DATE(bs_mapping_profit_sumusd_bwd.mapped) = '$date_filter'";
                        $rst_usd = $dbc->Query($sql_usd);
                        $row_usd = $dbc->Fetch($rst_usd);
                        $totalordersusd = (float)($row_usd['totalordersusd'] ?? 0);
                        $count_usd_orders = (int)($row_usd['count_orders'] ?? 0);

                        $sql_thb = "SELECT COUNT(bs_mapping_profit_orders_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_bwd.total) AS totalordersthb
                                    FROM `bs_mapping_profit_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_bwd
                                        ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                    WHERE DATE(bs_mapping_profit_bwd.mapped) = '$date_filter'";
                        $rst_thb = $dbc->Query($sql_thb);
                        $row_thb = $dbc->Fetch($rst_thb);
                        $totalordersthb = (float)($row_thb['totalordersthb'] ?? 0);
                        $count_thb_orders = (int)($row_thb['count_orders'] ?? 0);

                        $order_count = $count_usd_orders + $count_thb_orders;

                        $sql_usd_amount = "SELECT SUM(bs_mapping_profit_orders_usd_bwd.amount) AS total_amount_usd
                                          FROM `bs_mapping_profit_sumusd_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                              ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                          WHERE DATE(bs_mapping_profit_sumusd_bwd.mapped) = '$date_filter'";
                        $rst_usd_amount = $dbc->Query($sql_usd_amount);
                        $row_usd_amount = $dbc->Fetch($rst_usd_amount);
                        $total_amount_usd = (float)($row_usd_amount['total_amount_usd'] ?? 0);

                        $sql_thb_amount = "SELECT SUM(bs_mapping_profit_orders_bwd.amount) AS total_amount_thb
                                          FROM `bs_mapping_profit_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_bwd
                                              ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                          WHERE DATE(bs_mapping_profit_bwd.mapped) = '$date_filter'";
                        $rst_thb_amount = $dbc->Query($sql_thb_amount);
                        $row_thb_amount = $dbc->Fetch($rst_thb_amount);
                        $total_amount_thb = (float)($row_thb_amount['total_amount_thb'] ?? 0);

                        $order_amount = $total_amount_usd + $total_amount_thb;
                        $order_value = $totalordersusd + $totalordersthb;
                        $margin_amount -= $order_amount;

                        $sql_usd_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount,
                                                SUM(amount * rate_finance) AS usd_true
                                         FROM bs_purchase_usd_profit_bwd
                                         WHERE (bs_purchase_usd_profit_bwd.value_date = '$date_filter')
                                           AND (bs_purchase_usd_profit_bwd.status <> -1)
                                           AND (bs_purchase_usd_profit_bwd.type = 'physical' OR bs_purchase_usd_profit_bwd.type = 'MTM')
                                           AND bs_purchase_usd_profit_bwd.comment = 'BWD'
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01' ";
                        $rst_usd_true = $dbc->Query($sql_usd_true);
                        $row_usd_true = $dbc->Fetch($rst_usd_true);
                        $usd_true = (float)($row_usd_true['usd_true'] ?? 0);
                        $count_usd_purchase = (int)($row_usd_true['count_purchase'] ?? 0);
                        $amount_usd_purchase = (float)($row_usd_true['total_amount'] ?? 0);

                        $sql_purchase_usd_value = "SELECT SUM(bs_purchase_spot_profit_bwd.amount*32.1507*(bs_purchase_spot_profit.rate_spot+bs_purchase_spot_profit.rate_pmdc)) AS total,
                                                          SUM(bs_purchase_spot_profit.amount) AS amount
                                                  FROM bs_purchase_spot_profit_bwd
                                                  WHERE (bs_purchase_spot_profit_bwd.type LIKE 'physical'
                                                      OR bs_purchase_spot_profit_bwd.type LIKE 'MTM'
                                                      ) AND bs_purchase_spot_profit_bwd.status > 0 
                                                    AND bs_purchase_spot_profit_bwd.currency = 'USD'
                                                    AND flag_hide = 0 
                                                    AND bs_purchase_spot_profit_bwd.ref = 'BWD' 
                                                    AND bs_purchase_spot_profit_bwd.date >= '2025-10-01' 
                                                    AND bs_purchase_spot_profit_bwd.value_date = '$date_filter'";
                        $rst_purchase_usd_value = $dbc->Query($sql_purchase_usd_value);
                        $row_purchase_usd_value = $dbc->Fetch($rst_purchase_usd_value);
                        $purchase_usd_calculated = (float)($row_purchase_usd_value['total'] ?? 0);

                        $sql_purchase = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE (bs_purchase_spot_profit_bwd.value_date = '$date_filter')
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.ref = 'BWD' 
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'  AND bs_purchase_spot_profit_bwd.currency = 'USD'";
                        $rst_purchase = $dbc->Query($sql_purchase);
                        $row_purchase = $dbc->Fetch($rst_purchase);
                        $amount_purchase = (float)($row_purchase['total_amount'] ?? 0);

                        $sql_thb_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_thb_amount,
                                                SUM(THBValue) AS thb_true
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE (bs_purchase_spot_profit_bwd.value_date = '$date_filter')
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.ref LIKE '%แท่งดี%'
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.supplier_id = '28'
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01' AND bs_purchase_spot_profit_bwd.currency = 'THB'";
                        $rst_thb_true = $dbc->Query($sql_thb_true);
                        $row_thb_true = $dbc->Fetch($rst_thb_true);
                        $thb_true = (float)($row_thb_true['thb_true'] ?? 0);
                        $count_thb_purchase = (int)($row_thb_true['count_purchase'] ?? 0);
                        $amount_thb_purchase = (float)($row_thb_true['total_thb_amount'] ?? 0);

                        $purchase_count = $count_usd_purchase + $count_thb_purchase;
                        $purchase_amount = $amount_thb_purchase + $amount_purchase;
                        $purchase_usd_value = $purchase_usd_calculated;
                        $purchase_thb_value = $thb_true;
                        $margin_amount += $purchase_amount;
                        $margin_usd -= $purchase_usd_value;

                        $usd_purchase_count = $count_usd_purchase;
                        $usd_purchase_amount = $amount_usd_purchase;
                        $usd_purchase_thb_value = $usd_true;
                        $margin_usd += $usd_purchase_amount;

                        $balance_amount += $margin_amount;
                        $balance_usd += $margin_usd;

                        $total_profit = ($totalordersusd - $usd_true) + ($totalordersthb - $thb_true);
                        $total_all_profit += $total_profit;
                        $profit_color = ($total_profit < 0) ? 'text-danger' : '';

                        echo '<td class="text-center">' . number_format($order_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_value, 2) . '</td>';

                        echo '<td class="text-center">' . number_format($purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_usd_value, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_thb_value, 2) . '</td>';

                        echo '<td class="text-center">' . number_format($usd_purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_amount, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_thb_value, 2) . '</td>';

                        $margin_class = ($margin_amount > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_amount, 4) . '</td>';

                        $margin_class = ($margin_usd > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_usd, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_usd, 2) . '</td>';

                        echo '<td class="text-right pr-2 ' . $profit_color . '">' . number_format($total_profit, 2) . '</td>';

                        echo '</tr>';
                    }

                    $total_color = ($total_all_profit < 0) ? 'text-danger' : 'text-success';
                    echo '<tr class="table-info">';
                    echo '<td class="text-center checkbox-cell font-weight-bold"></td>';
                    echo '<td class="text-center font-weight-bold">TOTAL</td>';
                    echo '<td colspan="14" class="text-center font-weight-bold">Summary</td>';
                    echo '<td class="text-right pr-2 font-weight-bold ' . $total_color . '">' . number_format($total_all_profit, 2) . '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr><td colspan="16" class="text-center">No dates available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.checkboxData = window.checkboxData || {};
            window.checkboxData.currentReportType = 'daily';
            console.log('Enhanced Daily view initialized');
        });
    </script>

<?php
} else if ($_POST['type'] == "monthly") {
?>

    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">BWD Monthly Profit Summary </h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>

        <div class="bulk-actions">
            <button type="button" class="btn btn-sm btn-primary" onclick="checkAll()">
                <i class="fas fa-check-square"></i> Check All
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="uncheckAll()">
                <i class="far fa-square"></i> Uncheck All
            </button>
            <span class="ml-3 text-muted">
                Status: <span id="checkbox-counter">0 of 0 checked</span>
            </span>
        </div>

        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center table-dark" colspan="17">Profit Loss BWD Monthly Report</th>
                </tr>
                <tr>
                    <th class="text-center checkbox-cell" rowspan="2">✓</th>
                    <th class="text-center" rowspan="2">Date</th>
                    <th class="text-center" colspan="3">Sales</th>
                    <th class="text-center" colspan="4">Purchase Spot</th>
                    <th class="text-center" colspan="3">Purchase USD</th>
                    <th class="text-center" colspan="4">Margin</th>
                    <th class="text-center" rowspan="2">Profit(THB)</th>
                </tr>
                <tr>
                    <th class="text-center">Total Order</th>
                    <th class="text-center">Amount (KG)</th>
                    <th class="text-center">Total Value (THB)</th>

                    <th class="text-center">Total Purchase</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">USD Match</th>
                    <th class="text-center">THB Match</th>

                    <th class="text-center">Purchase</th>
                    <th class="text-center">Used USD</th>
                    <th class="text-center">THB (Purchase USD)</th>

                    <th class="text-center">Amount Margin</th>
                    <th class="text-center">Amount Balance</th>
                    <th class="text-center">USD Margin</th>
                    <th class="text-center">USD Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_dates = "SELECT DISTINCT DATE_FORMAT(mapped, '%Y-%m') AS report_date
                              FROM bs_mapping_profit_sumusd_bwd
                              WHERE 1=1 $month_condition
                              UNION
                              SELECT DISTINCT DATE_FORMAT(mapped, '%Y-%m') AS report_date
                              FROM bs_mapping_profit_bwd
                              WHERE 1=1 $month_condition
                              ORDER BY report_date ASC";
                $rst_dates = $dbc->Query($sql_dates);
                $total_sales_amount = 0;
                $total_sales_value = 0;
                $total_all_profit = 0;
                $balance_amount = 0;
                $balance_usd = 0;

                if ($rst_dates) {
                    while ($row_date = $dbc->Fetch($rst_dates)) {
                        $report_date = $row_date['report_date'];
                        $date_filter = $report_date;

                        $month_filter_condition = "DATE_FORMAT(mapped, '%Y-%m') = '$date_filter'";
                        $purchase_month_filter = "DATE_FORMAT(value_date, '%Y-%m') = '$date_filter'";

                        $is_checked = getMonthlyCheckboxStatus($dbc, $report_date);
                        $row_class = $is_checked ? 'row-checked' : '';
                        echo '<tr class="' . $row_class . '">';
                        echo '<td class="text-center checkbox-cell">';
                        echo '<input type="checkbox" data-date="' . $report_date . '" ' . ($is_checked ? 'checked' : '') . ' onchange="toggleRowCheck(this, \'' . $report_date . '\')">';
                        echo '</td>';

                        $display_month = date("M Y", strtotime($report_date . '-01'));
                        echo '<td class="text-center">' . $display_month . '</td>';

                        $order_count = 0;
                        $order_amount = 0;
                        $order_value = 0;

                        $purchase_count = 0;
                        $purchase_amount = 0;
                        $purchase_usd_value = 0;
                        $purchase_thb_value = 0;

                        $usd_purchase_count = 0;
                        $usd_purchase_amount = 0;
                        $usd_purchase_thb_value = 0;

                        $margin_amount = 0;
                        $margin_usd = 0;

                        $sql_usd = "SELECT COUNT(bs_mapping_profit_orders_usd_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_usd_bwd.total) AS totalordersusd
                                    FROM `bs_mapping_profit_sumusd_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                        ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                    WHERE $month_filter_condition";
                        $rst_usd = $dbc->Query($sql_usd);
                        $row_usd = $dbc->Fetch($rst_usd);
                        $totalordersusd = (float)($row_usd['totalordersusd'] ?? 0);
                        $count_usd_orders = (int)($row_usd['count_orders'] ?? 0);

                        $sql_thb = "SELECT COUNT(bs_mapping_profit_orders_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_bwd.total) AS totalordersthb
                                    FROM `bs_mapping_profit_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_bwd
                                        ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                    WHERE $month_filter_condition";
                        $rst_thb = $dbc->Query($sql_thb);
                        $row_thb = $dbc->Fetch($rst_thb);
                        $totalordersthb = (float)($row_thb['totalordersthb'] ?? 0);
                        $count_thb_orders = (int)($row_thb['count_orders'] ?? 0);

                        $order_count = $count_usd_orders + $count_thb_orders;

                        $sql_usd_amount = "SELECT SUM(bs_mapping_profit_orders_usd_bwd.amount) AS total_amount_usd
                                          FROM `bs_mapping_profit_sumusd_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                              ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                          WHERE $month_filter_condition";
                        $rst_usd_amount = $dbc->Query($sql_usd_amount);
                        $row_usd_amount = $dbc->Fetch($rst_usd_amount);
                        $total_amount_usd = (float)($row_usd_amount['total_amount_usd'] ?? 0);

                        $sql_thb_amount = "SELECT SUM(bs_mapping_profit_orders_bwd.amount) AS total_amount_thb
                                          FROM `bs_mapping_profit_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_bwd
                                              ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                          WHERE $month_filter_condition";
                        $rst_thb_amount = $dbc->Query($sql_thb_amount);
                        $row_thb_amount = $dbc->Fetch($rst_thb_amount);
                        $total_amount_thb = (float)($row_thb_amount['total_amount_thb'] ?? 0);

                        $order_amount = $total_amount_usd + $total_amount_thb;
                        $order_value = $totalordersusd + $totalordersthb;
                        $total_sales_amount += $order_amount;
                        $total_sales_value += $order_value;
                        $margin_amount -= $order_amount;

                        $sql_usd_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount,
                                                SUM(amount * rate_finance) AS usd_true
                                         FROM bs_purchase_usd_profit_bwd
                                         WHERE ($purchase_month_filter)
                                           AND (bs_purchase_usd_profit_bwd.status <> -1)
                                           AND (bs_purchase_usd_profit_bwd.type = 'physical' OR bs_purchase_usd_profit_bwd.type = 'MTM')
                                             AND bs_purchase_usd_profit_bwd.comment = 'BWD'
                                           AND bs_purchase_usd_profit_bwd.date >= '2025-10-01'";
                        $rst_usd_true = $dbc->Query($sql_usd_true);
                        $row_usd_true = $dbc->Fetch($rst_usd_true);
                        $usd_true = (float)($row_usd_true['usd_true'] ?? 0);
                        $count_usd_purchase = (int)($row_usd_true['count_purchase'] ?? 0);
                        $amount_usd_purchase = (float)($row_usd_true['total_amount'] ?? 0);

                        $sql_purchase_usd_value = "SELECT SUM(bs_purchase_spot_profit_bwd.amount*32.1507*(bs_purchase_spot_profit_bwd.rate_spot+bs_purchase_spot_profit_bwd.rate_pmdc)) AS total,
                                                          SUM(bs_purchase_spot_profit_bwd.amount) AS amount
                                                  FROM bs_purchase_spot_profit_bwd 
                                                  WHERE (bs_purchase_spot_profit_bwd.type LIKE 'physical'
                                                      OR bs_purchase_spot_profit_bwd.type LIKE 'MTM'
                                                      ) AND bs_purchase_spot_profit_bwd.status > 0 
                                                    AND bs_purchase_spot_profit_bwd.currency = 'USD'
                                                    AND bs_purchase_spot_profit_bwd.ref = 'BWD'
                                                    AND flag_hide = 0 
                                                    AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                                    AND ($purchase_month_filter)";
                        $rst_purchase_usd_value = $dbc->Query($sql_purchase_usd_value);
                        $row_purchase_usd_value = $dbc->Fetch($rst_purchase_usd_value);
                        $purchase_usd_calculated = (float)($row_purchase_usd_value['total'] ?? 0);

                        $sql_purchase = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE ($purchase_month_filter)
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.currency = 'USD'
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                           AND bs_purchase_spot_profit_bwd.ref = 'BWD'";
                        $rst_purchase = $dbc->Query($sql_purchase);
                        $row_purchase = $dbc->Fetch($rst_purchase);
                        $amount_purchase = (float)($row_purchase['total_amount'] ?? 0);

                        $sql_thb_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_thb_amount,
                                                SUM(THBValue) AS thb_true
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE ($purchase_month_filter)
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.currency = 'THB'
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                           AND bs_purchase_spot_profit_bwd.ref LIKE '%แท่งดี%' AND bs_purchase_spot_profit_bwd.supplier_id = '28'";
                        $rst_thb_true = $dbc->Query($sql_thb_true);
                        $row_thb_true = $dbc->Fetch($rst_thb_true);
                        $thb_true = (float)($row_thb_true['thb_true'] ?? 0);
                        $count_thb_purchase = (int)($row_thb_true['count_purchase'] ?? 0);
                        $amount_thb_purchase = (float)($row_thb_true['total_thb_amount'] ?? 0);

                        $purchase_count = $count_usd_purchase + $count_thb_purchase;
                        $purchase_amount = $amount_thb_purchase + $amount_purchase;
                        $purchase_usd_value = $purchase_usd_calculated;
                        $purchase_thb_value = $thb_true;
                        $margin_amount += $purchase_amount;
                        $margin_usd -= $purchase_usd_value;

                        $usd_purchase_count = $count_usd_purchase;
                        $usd_purchase_amount = $amount_usd_purchase;
                        $usd_purchase_thb_value = $usd_true;
                        $margin_usd += $usd_purchase_amount;

                        $balance_amount += $margin_amount;
                        $balance_usd += $margin_usd;

                        $total_profit = ($totalordersusd - $usd_true) + ($totalordersthb - $thb_true);
                        $total_all_profit += $total_profit;
                        $profit_color = ($total_profit < 0) ? 'text-danger' : '';

                        // Sales columns
                        echo '<td class="text-center">' . number_format($order_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_value, 2) . '</td>';

                        // Purchase columns
                        echo '<td class="text-center">' . number_format($purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_usd_value, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_thb_value, 2) . '</td>';

                        // USD Purchase columns
                        echo '<td class="text-center">' . number_format($usd_purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_amount, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_thb_value, 2) . '</td>';

                        // Margin columns
                        $margin_class = ($margin_amount > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_amount, 4) . '</td>';

                        $margin_class = ($margin_usd > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_usd, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_usd, 2) . '</td>';

                        // Profit column
                        echo '<td class="text-right pr-2 ' . $profit_color . '">' . number_format($total_profit, 2) . '</td>';

                        echo '</tr>';
                    }

                    $total_color = ($total_all_profit < 0) ? 'text-danger' : 'text-success';
                    echo '<tr class="table-info">';
                    echo '<td class="text-center checkbox-cell font-weight-bold"></td>';
                    echo '<td class="text-center font-weight-bold">TOTAL</td>';
                    echo '<td class="text-right pr-2 font-weight-bold"></td>';
                    echo '<td class="text-right pr-2 font-weight-bold">' . number_format($total_sales_amount, 4) . '</td>';
                    echo '<td class="text-right pr-2 font-weight-bold">' . number_format($total_sales_value, 2) . '</td>';
                    echo '<td colspan="11" class="text-center font-weight-bold">Summary</td>';
                    echo '<td class="text-right pr-2 font-weight-bold ' . $total_color . '">' . number_format($total_all_profit, 2) . '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr><td colspan="17" class="text-center">No months available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.checkboxData = window.checkboxData || {};
            window.checkboxData.currentReportType = 'monthly';
            console.log('Enhanced Monthly view initialized');
        });
    </script>

<?php
} else {
?>
    <div class="col-xl-12 mb-12">
        <h4 class="mb-3 text-center">BWD Profit - Default</h4>
        <?php if ($month_filter): ?>
            <p class="text-center text-muted">Filtered from: <?php echo date("F Y", strtotime($month_filter . '-01')); ?></p>
        <?php endif; ?>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <button type="button" class="btn btn-sm btn-primary" onclick="checkAll()">
                <i class="fas fa-check-square"></i> Check All
            </button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="uncheckAll()">
                <i class="far fa-square"></i> Uncheck All
            </button>
            <span class="ml-3 text-muted">
                Status: <span id="checkbox-counter">0 of 0 checked</span>
            </span>
        </div>

        <table class="table table-striped table-sm table-bordered dt-responsive nowrap">
            <thead>
                <tr>
                    <th class="text-center table-dark" colspan="17">Profit Loss Bowins Design - Default</th>
                </tr>
                <tr>
                    <th class="text-center checkbox-cell" rowspan="2">✓</th>
                    <th class="text-center" rowspan="2">Date</th>
                    <th class="text-center" colspan="3">Sales</th>
                    <th class="text-center" colspan="4">Purchase Spot</th>
                    <th class="text-center" colspan="3">Purchase USD</th>
                    <th class="text-center" colspan="4">Margin</th>
                    <th class="text-center" rowspan="2">Profit(THB)</th>
                </tr>
                <tr>
                    <th class="text-center">Total Order</th>
                    <th class="text-center">Amount (KG)</th>
                    <th class="text-center">Total Value (THB)</th>

                    <th class="text-center">Total Purchase</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">USD Match</th>
                    <th class="text-center">THB Match</th>

                    <th class="text-center">Purchase</th>
                    <th class="text-center">Used USD</th>
                    <th class="text-center">THB (Purchase USD)</th>

                    <th class="text-center">Amount Margin</th>
                    <th class="text-center">Amount Balance</th>
                    <th class="text-center">USD Margin</th>
                    <th class="text-center">USD Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_dates = "SELECT DISTINCT DATE(mapped) AS report_date
                              FROM bs_mapping_profit_sumusd_bwd
                              WHERE 1=1 $month_condition
                              UNION
                              SELECT DISTINCT DATE(mapped) AS report_date
                              FROM bs_mapping_profit_bwd
                              WHERE 1=1 $month_condition
                              ORDER BY report_date ASC";
                $rst_dates = $dbc->Query($sql_dates);
                $total_sales_amount = 0;
                $total_sales_value = 0;
                $total_all_profit = 0;
                $balance_amount = 0;
                $balance_usd = 0;

                if ($rst_dates) {
                    while ($row_date = $dbc->Fetch($rst_dates)) {
                        $report_date = $row_date['report_date'];
                        $date_filter = $report_date;

                        $is_checked = getCheckboxStatus($dbc, $report_date);
                        $row_class = $is_checked ? 'row-checked' : '';
                        echo '<tr class="' . $row_class . '">';
                        echo '<td class="text-center checkbox-cell">';
                        echo '<input type="checkbox" data-date="' . $report_date . '" ' . ($is_checked ? 'checked' : '') . ' onchange="toggleRowCheck(this, \'' . $report_date . '\')">';
                        echo '</td>';
                        echo '<td class="text-center">' . $report_date . '</td>';

                        // Initialize variables
                        $order_count = 0;
                        $order_amount = 0;
                        $order_value = 0;

                        $purchase_count = 0;
                        $purchase_amount = 0;
                        $purchase_usd_value = 0;
                        $purchase_thb_value = 0;

                        $usd_purchase_count = 0;
                        $usd_purchase_amount = 0;
                        $usd_purchase_thb_value = 0;

                        $margin_amount = 0;
                        $margin_usd = 0;

                        $sql_usd = "SELECT COUNT(bs_mapping_profit_orders_usd_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_usd_bwd.total) AS totalordersusd
                                    FROM `bs_mapping_profit_sumusd_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                        ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                    WHERE DATE(bs_mapping_profit_sumusd_bwd.mapped) = '$date_filter'";
                        $rst_usd = $dbc->Query($sql_usd);
                        $row_usd = $dbc->Fetch($rst_usd);
                        $totalordersusd = (float)($row_usd['totalordersusd'] ?? 0);
                        $count_usd_orders = (int)($row_usd['count_orders'] ?? 0);

                        $sql_thb = "SELECT COUNT(bs_mapping_profit_orders_bwd.id) as count_orders,
                                           SUM(bs_mapping_profit_orders_bwd.total) AS totalordersthb
                                    FROM `bs_mapping_profit_bwd`
                                    LEFT JOIN bs_mapping_profit_orders_bwd
                                        ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                    WHERE DATE(bs_mapping_profit_bwd.mapped) = '$date_filter'";
                        $rst_thb = $dbc->Query($sql_thb);
                        $row_thb = $dbc->Fetch($rst_thb);
                        $totalordersthb = (float)($row_thb['totalordersthb'] ?? 0);
                        $count_thb_orders = (int)($row_thb['count_orders'] ?? 0);

                        // Calculate Sales totals
                        $order_count = $count_usd_orders + $count_thb_orders;

                        // Calculate Amount (KG) for sales - need to get actual amount data
                        $sql_usd_amount = "SELECT SUM(bs_mapping_profit_orders_usd_bwd.amount) AS total_amount_usd
                                          FROM `bs_mapping_profit_sumusd_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_usd_bwd
                                              ON bs_mapping_profit_orders_usd_bwd.mapping_id = bs_mapping_profit_sumusd_bwd.id
                                          WHERE DATE(bs_mapping_profit_sumusd_bwd.mapped) = '$date_filter'";
                        $rst_usd_amount = $dbc->Query($sql_usd_amount);
                        $row_usd_amount = $dbc->Fetch($rst_usd_amount);
                        $total_amount_usd = (float)($row_usd_amount['total_amount_usd'] ?? 0);

                        $sql_thb_amount = "SELECT SUM(bs_mapping_profit_orders_bwd.amount) AS total_amount_thb
                                          FROM `bs_mapping_profit_bwd`
                                          LEFT JOIN bs_mapping_profit_orders_bwd
                                              ON bs_mapping_profit_orders_bwd.mapping_id = bs_mapping_profit_bwd.id
                                          WHERE DATE(bs_mapping_profit_bwd.mapped) = '$date_filter'";
                        $rst_thb_amount = $dbc->Query($sql_thb_amount);
                        $row_thb_amount = $dbc->Fetch($rst_thb_amount);
                        $total_amount_thb = (float)($row_thb_amount['total_amount_thb'] ?? 0);

                        $order_amount = $total_amount_usd + $total_amount_thb;
                        $order_value = $totalordersusd + $totalordersthb;
                        $total_sales_amount += $order_amount;
                        $total_sales_value += $order_value;
                        $margin_amount -= $order_amount;

                        $sql_usd_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount,
                                                SUM(amount * rate_finance) AS usd_true
                                         FROM bs_purchase_usd_profit_bwd
                                         WHERE (bs_purchase_usd_profit_bwd.value_date = '$date_filter')
                                           AND (bs_purchase_usd_profit_bwd.status <> -1)
                                           AND (bs_purchase_usd_profit_bwd.type = 'physical' OR bs_purchase_usd_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_usd_profit_bwd.comment = 'BWD')
                                           AND (bs_purchase_usd_profit_bwd.date >= '2025-10-01')";
                        $rst_usd_true = $dbc->Query($sql_usd_true);
                        $row_usd_true = $dbc->Fetch($rst_usd_true);
                        $usd_true = (float)($row_usd_true['usd_true'] ?? 0);
                        $count_usd_purchase = (int)($row_usd_true['count_purchase'] ?? 0);
                        $amount_usd_purchase = (float)($row_usd_true['total_amount'] ?? 0);

                        $sql_purchase_usd_value = "SELECT SUM(bs_purchase_spot_profit_bwd.amount*32.1507*(bs_purchase_spot_profit_bwd.rate_spot+bs_purchase_spot_profit_bwd.rate_pmdc)) AS total,
                                                          SUM(bs_purchase_spot_profit_bwd.amount) AS amount
                                                  FROM bs_purchase_spot_profit_bwd 
                                                  WHERE (bs_purchase_spot_profit_bwd.type LIKE 'physical'
                                                      OR bs_purchase_spot_profit_bwd.type LIKE 'MTM'
                                                      ) AND bs_purchase_spot_profit_bwd.status > 0 
                                                    AND bs_purchase_spot_profit_bwd.currency = 'USD'
                                                    AND flag_hide = 0 
                                                    AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                                    AND bs_purchase_spot_profit_bwd.ref = 'BWD'
                                                    AND bs_purchase_spot_profit_bwd.value_date = '$date_filter'";
                        $rst_purchase_usd_value = $dbc->Query($sql_purchase_usd_value);
                        $row_purchase_usd_value = $dbc->Fetch($rst_purchase_usd_value);
                        $purchase_usd_calculated = (float)($row_purchase_usd_value['total'] ?? 0);

                        $sql_purchase = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_amount
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE (bs_purchase_spot_profit_bwd.value_date = '$date_filter')
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                           AND bs_purchase_spot_profit_bwd.ref = 'BWD'
                                           AND bs_purchase_spot_profit_bwd.currency = 'USD'";
                        $rst_purchase = $dbc->Query($sql_purchase);
                        $row_purchase = $dbc->Fetch($rst_purchase);
                        $amount_purchase = (float)($row_purchase['total_amount'] ?? 0);

                        $sql_thb_true = "SELECT COUNT(id) as count_purchase,
                                                SUM(amount) as total_thb_amount,
                                                SUM(THBValue) AS thb_true
                                         FROM bs_purchase_spot_profit_bwd
                                         WHERE (bs_purchase_spot_profit_bwd.value_date = '$date_filter')
                                           AND bs_purchase_spot_profit_bwd.parent IS NULL
                                           AND flag_hide = 0
                                           AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                           AND (bs_purchase_spot_profit_bwd.type = 'physical' OR bs_purchase_spot_profit_bwd.type = 'MTM')
                                           AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                           AND bs_purchase_spot_profit_bwd.date >= '2025-10-01'
                                           AND bs_purchase_spot_profit_bwd.ref  LIKE '%แท่งดี%'
                                           AND bs_purchase_spot_profit_bwd.supplier_id = '28'
                                           AND bs_purchase_spot_profit_bwd.currency = 'THB'";
                        $rst_thb_true = $dbc->Query($sql_thb_true);
                        $row_thb_true = $dbc->Fetch($rst_thb_true);
                        $thb_true = (float)($row_thb_true['thb_true'] ?? 0);
                        $count_thb_purchase = (int)($row_thb_true['count_purchase'] ?? 0);
                        $amount_thb_purchase = (float)($row_thb_true['total_thb_amount'] ?? 0);

                        $purchase_count = $count_usd_purchase + $count_thb_purchase;
                        $purchase_amount = $amount_thb_purchase + $amount_purchase;
                        $purchase_usd_value = $purchase_usd_calculated;
                        $purchase_thb_value = $thb_true;
                        $margin_amount += $purchase_amount;
                        $margin_usd -= $purchase_usd_value;

                        $usd_purchase_count = $count_usd_purchase;
                        $usd_purchase_amount = $amount_usd_purchase;
                        $usd_purchase_thb_value = $usd_true;
                        $margin_usd += $usd_purchase_amount;

                        $balance_amount += $margin_amount;
                        $balance_usd += $margin_usd;

                        $total_profit = ($totalordersusd - $usd_true) + ($totalordersthb - $thb_true);
                        $total_all_profit += $total_profit;
                        $profit_color = ($total_profit < 0) ? 'text-danger' : '';

                        echo '<td class="text-center">' . number_format($order_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($order_value, 2) . '</td>';

                        // Purchase columns
                        echo '<td class="text-center">' . number_format($purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_usd_value, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($purchase_thb_value, 2) . '</td>';

                        // USD Purchase columns
                        echo '<td class="text-center">' . number_format($usd_purchase_count, 0) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_amount, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($usd_purchase_thb_value, 2) . '</td>';

                        $margin_class = ($margin_amount > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_amount, 4) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_amount, 4) . '</td>';

                        $margin_class = ($margin_usd > 0) ? " text-success" : " text-danger";
                        echo '<td class="text-right pr-2' . $margin_class . '">' . number_format($margin_usd, 2) . '</td>';
                        echo '<td class="text-right pr-2">' . number_format($balance_usd, 2) . '</td>';

                        echo '<td class="text-right pr-2 ' . $profit_color . '">' . number_format($total_profit, 2) . '</td>';

                        echo '</tr>';
                    }

                    // Summary row
                    $total_color = ($total_all_profit < 0) ? 'text-danger' : 'text-success';
                    echo '<tr class="table-info">';
                    echo '<td class="text-center checkbox-cell font-weight-bold"></td>';
                    echo '<td class="text-center font-weight-bold">TOTAL</td>';
                    echo '<td class="text-right pr-2 font-weight-bold"></td>';
                    echo '<td class="text-right pr-2 font-weight-bold">' . number_format($total_sales_amount, 4) . '</td>';
                    echo '<td class="text-right pr-2 font-weight-bold">' . number_format($total_sales_value, 2) . '</td>';
                    echo '<td colspan="11" class="text-center font-weight-bold">Summary</td>';
                    echo '<td class="text-right pr-2 font-weight-bold ' . $total_color . '">' . number_format($total_all_profit, 2) . '</td>';
                    echo '</tr>';
                } else {
                    echo '<tr><td colspan="17" class="text-center">No dates available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.checkboxData = window.checkboxData || {};
            window.checkboxData.currentReportType = 'daily';
            console.log('Enhanced Default view initialized');
        });
    </script>
<?php
}

$dbc->Close();
?>