<?php
// ============================================================================
// Bank Transfer Report - ปรับปรุงให้อ่านง่าย
// ============================================================================

// Helper Functions
function DateDiff($strDate1, $strDate2) {
    return (strtotime($strDate2) - strtotime($strDate1)) / (60 * 60 * 24);
}

// Configuration
$banks = [
    'SCB' => ['color' => 'purple', 'bg_class' => 'bg-purple'],
    'BBL' => ['color' => 'blue', 'bg_class' => 'bg-blue'],
    'KBANK' => ['color' => 'green', 'bg_class' => 'bg-success'],
    'BAY' => ['color' => 'orange', 'bg_class' => 'bg-warning']
];

// ============================================================================
// Main Functions
// ============================================================================

function getTransfersByDate($dbc, $bank, $date) {
    $sql = "SELECT DISTINCT(bs_transfers.id), bs_transfers.code, bs_transfers.bank, bs_transfers.date,
            bs_transfers.type, bs_transfers.supplier_id, bs_transfers.value_usd_goods,
            bs_transfers.value_usd_deposit, bs_transfers.value_usd_paid, bs_transfers.value_usd_adjusted,
            bs_transfers.value_usd_total, bs_transfers.value_usd_fixed, bs_transfers.value_usd_nonfixed,
            bs_transfers.rate_counter, bs_transfers.value_thb_fixed, bs_transfers.value_thb_premium,
            bs_transfers.value_thb_net, bs_transfers.remark, bs_transfers.value_thb_transaction,
            bs_transfers.paid_thb, bs_transfers.paid_usd, bs_transfers.status, bs_transfers.source,
            bs_transfers.rate_interest, bs_transfers.due_date
            FROM bs_transfers 
            LEFT OUTER JOIN bs_transfer_payments ON bs_transfers.id = bs_transfer_payments.transfer_id
            WHERE bs_transfers.bank = '{$bank}' AND bs_transfer_payments.date = '{$date}'";
    
    return $dbc->Query($sql);
}

function getTransfersByCode($dbc, $bank, $date) {
    $sql = "SELECT * FROM bs_transfers 
            WHERE bank = '{$bank}' 
            AND (value_thb_net != paid_thb OR value_usd_nonfixed != paid_usd) 
            AND date = '{$date}'";
    
    return $dbc->Query($sql);
}

function renderTransferMainRow($dbc, $transfer, $bgClass) {
    $supplier = $dbc->GetRecord("bs_suppliers", "*", "id=" . $transfer['supplier_id']);
    $payment = $dbc->GetRecord("bs_transfer_payments", "SUM(interest),SUM(paid)", "transfer_id=" . $transfer['id']);
    $total_usd_paid = $transfer['paid_usd'] + $transfer['value_usd_paid'];
    
    echo '<tr class="' . $bgClass . ' text-white">';
    echo '<td class="text-center">' . $transfer['date'] . '</td>';
    echo '<td class="text-center">' . $transfer['code'] . '</td>';
    echo '<td class="text-center">' . $transfer['remark'] . '</td>';
    echo '<td class="text-center">' . $transfer['rate_interest'] . '</td>';
    echo '<td class="text-center">' . $transfer['due_date'] . '</td>';
    echo '<td class="text-center">' . $transfer['type'] . '</td>';
    echo '<td class="text-center">' . $supplier['name'] . '</td>';
    echo '<td class="text-center">' . number_format($transfer['value_usd_total'] + $transfer['value_usd_paid'], 2) . '</td>';
    echo '<td class="text-center">' . number_format($transfer['value_usd_fixed'], 2) . '</td>';
    echo '<td class="text-center">' . number_format($transfer['value_usd_nonfixed'] - $total_usd_paid, 2) . '</td>';
    echo '<td class="text-center">' . number_format($transfer['value_thb_net'], 2) . '</td>';
    echo '<td class="text-center">' . number_format($transfer['paid_usd'] + $transfer['value_usd_paid'], 2) . '</td>';
    echo '<td class="text-center">' . number_format($transfer['paid_thb'], 2) . '</td>';
    echo '<td class="text-right pr-2">' . number_format($payment[0], 2) . '</td>';
    echo '</tr>';
}

function renderPaymentHeaders() {
    echo '<thead>';
    echo '<tr>';
    echo '<th class="text-center"></th>';
    echo '<th class="text-center" colspan="2">Date</th>';
    echo '<th class="text-center">Principle THB</th>';
    echo '<th class="text-center">THB Paid</th>';
    echo '<th class="text-center">Interest Rate</th>';
    echo '<th class="text-center">Interest Period</th>';
    echo '<th class="text-center">Interest</th>';
    echo '<th class="text-center">Total THB Paid</th>';
    echo '<th class="text-center">Principle USD</th>';
    echo '<th class="text-center">USD Paid</th>';
    echo '<th class="text-center">Counter Rate</th>';
    echo '<th class="text-center">Interest Paid(USD)</th>';
    echo '<th class="text-center" colspan="3"></th>';
    echo '</tr>';
    echo '</thead>';
}

function renderInitialPaymentRow($transfer) {
    echo '<tbody>';
    echo '<tr>';
    echo '<td class="text-center" colspan="8">จ่าย ณ วันตั้งหนี้</td>';
    echo '<td class="text-right pr-2">' . number_format($transfer['value_usd_paid'], 2) . '</td>';
    echo '<td class="text-center">ค่าธรรมเนียม</td>';
    echo '<td class="text-right pr-2">' . number_format($transfer['value_thb_transaction'], 2) . '</td>';
    echo '<td class="text-center">-</td>';
    echo '<td class="text-center"></td>';
    echo '<td class="text-center"></td>';
    echo '</tr>';
}

function renderPaymentRow($payment, $currentDate, $isDateFilter = true) {
    $isCurrentDate = ($payment['date'] == $currentDate);
    $displayStyle = $isDateFilter && !$isCurrentDate ? 'style="display:none;"' : '';
    $textClass = $isDateFilter && $isCurrentDate ? 'text-danger font-weight-bold' : '';
    
    echo '<tr>';
    echo '<td class="text-center" ' . $displayStyle . '></td>';
    echo '<td class="text-center ' . $textClass . '" colspan="2" ' . $displayStyle . '>' . $payment['date'] . '</td>';
    
    if ($payment['currency'] == "THB") {
        renderTHBPayment($payment, $displayStyle, $textClass);
    } else if ($payment['currency'] == "USD") {
        renderUSDPayment($payment, $displayStyle, $textClass);
    }
    
    echo '</tr>';
}

function renderTHBPayment($payment, $displayStyle, $textClass) {
    $totalthbpaid = $payment['paid'] + $payment['interest'];
    
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['principle'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['paid'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['rate_interest'], 2) . '</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . DateDiff($payment['date_from'], $payment['date_to']) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['interest'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($totalthbpaid, 2) . '</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center ' . $textClass . '" colspan="2" ' . $displayStyle . '>-</td>';
}

function renderUSDPayment($payment, $displayStyle, $textClass) {
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-center" ' . $displayStyle . '>-</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['principle'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['paid'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['rate_counter'], 2) . '</td>';
    echo '<td class="text-right pr-2 ' . $textClass . '" ' . $displayStyle . '>' . number_format($payment['interest'], 2) . '</td>';
    echo '<td class="text-center" colspan="2" ' . $displayStyle . '></td>';
}

function renderUSDHeaders() {
    echo '<thead>';
    echo '<tr>';
    echo '<th class="text-center"></th>';
    echo '<th class="text-center" colspan="2">Added Date</th>';
    echo '<th class="text-center">ID</th>';
    echo '<th class="text-center">Date</th>';
    echo '<th class="text-center">Premium Start</th>';
    echo '<th class="text-center">Premium End</th>';
    echo '<th class="text-center">Forward Contract No.</th>';
    echo '<th class="text-center">Rate Exchange</th>';
    echo '<th class="text-center">Premium</th>';
    echo '<th class="text-center">FX Rate + Premium</th>';
    echo '<th class="text-center">Amount</th>';
    echo '<th class="text-center">THB</th>';
    echo '<th class="text-center">THB + Premium</th>';
    echo '</tr>';
    echo '</thead>';
}

function renderUSDTransferRow($item, $purchase, $currentDate, $isDateFilter = true) {
    $thb = $purchase['rate_exchange'] * $purchase['amount'];
    $isCurrentDate = ($item['date'] == $currentDate);
    $displayStyle = $isDateFilter && !$isCurrentDate ? 'style="display:none;"' : '';
    $textClass = $isDateFilter && $isCurrentDate ? 'text-danger font-weight-bold' : '';
    
    echo '<tr>';
    echo '<th class="text-center" ' . $displayStyle . '></th>';
    echo '<td class="text-center ' . $textClass . '" colspan="2" ' . $displayStyle . '>' . $item['date'] . '</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $purchase['id'] . '</td>';
    echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $purchase['date'] . '</td>';
    
    if ($item['premium_type'] == 1) {
        echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $item['premium_start'] . '</td>';
        echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $item['premium_end'] . '</td>';
        echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $item['fw_contract_no'] . '</td>';
        echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . $purchase['rate_exchange'] . '</td>';
        echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . $item['premium'] . '</td>';
        echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . ($purchase['rate_exchange'] + $item['premium']) . '</td>';
    } else {
        echo '<td class="text-center" ' . $displayStyle . '>-</td>';
        echo '<td class="text-center" ' . $displayStyle . '>-</td>';
        echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $item['fw_contract_no'] . '</td>';
        echo '<td class="text-center ' . $textClass . '" ' . $displayStyle . '>' . $purchase['rate_exchange'] . '</td>';
        echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . $item['premium'] . '</td>';
        echo '<td class="text-right" ' . $displayStyle . '>-</td>';
    }
    
    echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . number_format($purchase['amount'], 2) . '</td>';
    echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . number_format($thb, 2) . '</td>';
    echo '<td class="text-right ' . $textClass . '" ' . $displayStyle . '>' . number_format($thb + $item['premium'], 2) . '</td>';
    echo '</tr>';
}

function renderUSDSummaryRow($total) {
    echo '<tr>';
    echo '<th class="text-right text-white bg-danger" colspan="9">รวม</th>';
    echo '<th class="text-right">' . number_format($total[0], 2) . '</th>';
    echo '<th class="text-right">' . number_format($total[1], 2) . '</th>';
    echo '<th class="text-right">' . number_format($total[2], 2) . '</th>';
    echo '<th class="text-right">' . number_format($total[3], 2) . '</th>';
    echo '<th class="text-right">' . number_format($total[4], 2) . '</th>';
    echo '</tr>';
}

function renderBankTable($dbc, $bank, $bankConfig, $date, $isDateFilter = true) {
    $tableClass = 'table-' . strtolower($bank);
    
    echo '<h1 class="text-center">' . $bank . '</h1>';
    echo '<table class="table ' . $tableClass . ' table-sm table-bordered">';
    
    // Table Header
    echo '<thead>';
    echo '<tr>';
    echo '<th class="text-center">TR Date</th>';
    echo '<th class="text-center">TR Code</th>';
    echo '<th class="text-center">Remark</th>';
    echo '<th class="text-center">Rate Interest</th>';
    echo '<th class="text-center">Due Date</th>';
    echo '<th class="text-center">Type</th>';
    echo '<th class="text-center">Supplier</th>';
    echo '<th class="text-center">Total USD Value</th>';
    echo '<th class="text-center">USD Fixed Value</th>';
    echo '<th class="text-center">USD Non-Fixed Value</th>';
    echo '<th class="text-center">Total THB Net</th>';
    echo '<th class="text-center">USD Paid</th>';
    echo '<th class="text-center">THB Paid</th>';
    echo '<th class="text-center">Interest</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Get and process transfers
    if ($isDateFilter) {
        $rst = getTransfersByDate($dbc, $bank, $date);
    } else {
        $rst = getTransfersByCode($dbc, $bank, $date);
    }
    
    while ($transfer = $dbc->Fetch($rst)) {
        // Main transfer row
        renderTransferMainRow($dbc, $transfer, $bankConfig['bg_class']);
        
        // Payment details
        renderPaymentHeaders();
        renderInitialPaymentRow($transfer);
        
        // Payment records
        $paymentSql = "SELECT * FROM bs_transfer_payments WHERE transfer_id=" . $transfer['id'];
        $paymentRst = $dbc->Query($paymentSql);
        
        while ($payment = $dbc->Fetch($paymentRst)) {
            renderPaymentRow($payment, $date, $isDateFilter);
        }
        
        // USD Transfer details
        renderUSDHeaders();
        echo '<tbody>';
        
        // Process USD transfers by date groups
        $groupSql = "SELECT date FROM bs_transfer_usd WHERE transfer_id = " . $transfer['id'] . " GROUP BY date";
        $groupRst = $dbc->Query($groupSql);
        
        while ($transferGroup = $dbc->Fetch($groupRst)) {
            if (!is_null($transferGroup['date'])) {
                $usdSql = "SELECT * FROM bs_transfer_usd WHERE transfer_id = " . $transfer['id'] . " AND date = '" . $transferGroup['date'] . "'";
                $usdRst = $dbc->Query($usdSql);
                $total = [0, 0, 0, 0, 0];
                
                while ($item = $dbc->Fetch($usdRst)) {
                    $purchase = $dbc->GetRecord("bs_purchase_usd", "*", "id=" . $item['purchase_id']);
                    $thb = $purchase['rate_exchange'] * $purchase['amount'];
                    
                    renderUSDTransferRow($item, $purchase, $date, $isDateFilter);
                    
                    // Calculate totals
                    $total[0] += $item['premium'];
                    if ($item['premium_type'] == 1) {
                        $total[1] += ($purchase['rate_exchange'] + $item['premium']);
                    }
                    $total[2] += $purchase['amount'];
                    $total[3] += $thb;
                    $total[4] += $thb + $item['premium'];
                }
                
                renderUSDSummaryRow($total);
            }
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '<br>';
}
?>

<style>
    /* Bank-specific table styles */
    table.table-scb { border: 1px solid purple; margin-top: 20px; }
    table.table-scb > thead > tr > th, 
    table.table-scb > tbody > tr > td { border: 1px solid purple; }

    table.table-kbank { border: 1px solid green; margin-top: 20px; }
    table.table-kbank > thead > tr > th, 
    table.table-kbank > tbody > tr > td { border: 1px solid green; }

    table.table-bbl { border: 1px solid blue; margin-top: 20px; }
    table.table-bbl > thead > tr > th, 
    table.table-bbl > tbody > tr > td { border: 1px solid blue; }

    table.table-bay { border: 1px solid orange; margin-top: 20px; }
    table.table-bay > thead > tr > th, 
    table.table-bay > tbody > tr > td { border: 1px solid orange; }

    /* Tab system styles */
    .tabs { max-width: 2024px; margin: 0 auto; padding: 0 20px; }
    #tab-button { display: table; table-layout: fixed; width: 100%; margin: 0; padding: 0; list-style: none; }
    #tab-button li { display: table-cell; width: 20%; }
    #tab-button li a {
        display: block; padding: .5em; background: #eee; border: 1px solid #ddd;
        text-align: center; color: #000; text-decoration: none;
    }
    #tab-button li:not(:first-child) a { border-left: none; }
    #tab-button li a:hover, #tab-button .is-active a { border-bottom-color: transparent; background: #fff; }
    .tab-contents { padding: .5em 2em 1em; border: 1px solid #ddd; margin-top: 20px; }
    .tab-button-outer { display: none; }

    @media screen and (min-width: 768px) {
        .tab-button-outer { position: relative; z-index: 2; display: block; }
        .tab-select-outer { display: none; }
        .tab-contents { position: relative; top: -1px; margin-top: 0; }
    }
</style>

<div class="tabs">
    <!-- Tab Navigation -->
    <div class="tab-button-outer">
        <ul id="tab-button">
            <li><a href="#tab01">ตรวจสอบจากวันที่ตัด</a></li>
            <li><a href="#tab02">ตรวจสอบจากตั๋ว</a></li>
        </ul>
    </div>
    
    <div class="tab-select-outer">
        <select id="tab-select">
            <option value="#tab01">ตรวจสอบจากวันที่ตัด</option>
            <option value="#tab02">ตรวจสอบจากตั๋ว</option>
        </select>
    </div>

    <!-- Tab 1: ตรวจสอบจากวันที่ตัด -->
    <div id="tab01" class="tab-contents">
        <?php
        foreach ($banks as $bankName => $bankConfig) {
            renderBankTable($dbc, $bankName, $bankConfig, $_POST['date'], true);
        }
        ?>
    </div>

    <!-- Tab 2: ตรวจสอบจากตั๋ว -->
    <div id="tab02" class="tab-contents">
        <?php
        foreach ($banks as $bankName => $bankConfig) {
            renderBankTable($dbc, $bankName, $bankConfig, $_POST['date'], false);
        }
        ?>
    </div>
</div>

<script>
$(function() {
    var $tabButtonItem = $('#tab-button li'),
        $tabSelect = $('#tab-select'),
        $tabContents = $('.tab-contents'),
        activeClass = 'is-active';

    // Initialize first tab as active
    $tabButtonItem.first().addClass(activeClass);
    $tabContents.not(':first').hide();

    // Tab button click handler
    $tabButtonItem.find('a').on('click', function(e) {
        var target = $(this).attr('href');
        
        $tabButtonItem.removeClass(activeClass);
        $(this).parent().addClass(activeClass);
        $tabSelect.val(target);
        $tabContents.hide();
        $(target).show();
        e.preventDefault();
    });

    // Select dropdown change handler
    $tabSelect.on('change', function() {
        var target = $(this).val(),
            targetSelectNum = $(this).prop('selectedIndex');
        
        $tabButtonItem.removeClass(activeClass);
        $tabButtonItem.eq(targetSelectNum).addClass(activeClass);
        $tabContents.hide();
        $(target).show();
    });
});
</script>