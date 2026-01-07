<?php

echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">';

//Custom css By Pattaragun Junthhomkai
echo '<style>
:root {
    --primary-color: #00204E;
    --secondary-color: #ffffff;
    --accent-color: #f8f9fa;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
}

body {
    margin: 0;
    color: #333;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.financial-dashboard {
    background: linear-gradient(135deg, var(--accent-color) 0%, #e9ecef 100%);
    min-height: 100vh;
    padding: 20px;
    box-sizing: border-box;
}

.dashboard-header {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 32, 78, 0.3);
    text-align: center;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 300;
    margin: 0;
}

.dashboard-subtitle {
    font-size: 1rem;
    opacity: 0.8;
    margin: 10px 0 0 0;
}

.report-section {
    background: var(--secondary-color);
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.section-header {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 15px 20px;
    font-size: 1.1rem;
    font-weight: 500;
    border-bottom: 3px solid #001a3d;
}

.table-container {
    padding: 20px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.financial-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
    margin: 0;
    min-width: 600px;
}

.financial-table th {
    background: var(--primary-color);
    color: var(--secondary-color);
    padding: 12px 8px;
    text-align: center;
    font-weight: 600;
    border: 1px solid #001a3d;
    font-size: 0.65rem;
    white-space: nowrap;
}

.financial-table td {
    padding: 10px 8px;
    border: 1px solid #dee2e6;
    text-align: center;
    white-space: nowrap;
}

.financial-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.financial-table tbody tr:hover {
    background-color: #e3f2fd;
    transition: background-color 0.3s ease;
}

.row-header {
    background: var(--accent-color) !important;
    font-weight: 600;
    text-align: left !important;
    color: var(--primary-color);
}

.sub-header {
    background: #f1f3f4 !important;
    font-weight: 500;
    text-align: left !important;
    padding-left: 20px !important;
}

.highlight-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
    color: #856404;
    font-weight: 600;
}

.highlight-success {
    background: linear-gradient(135deg, #d4edda 0%, #a8e6a1 100%) !important;
    color: #155724;
    font-weight: 600;
}

.highlight-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5a3aa 100%) !important;
    color: #721c24;
}

.text-positive {
    color: var(--success-color);
    font-weight: 600;
}

.text-negative {
    color: var(--danger-color);
    font-weight: 600;
}

.amount-cell {
    font-weight: 500;
}

.total-row {
    background: var(--primary-color) !important;
    color: var(--secondary-color) !important;
    font-weight: 700;
}

/* Summary Cards Layout */
.summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 0 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, #001a3d 100%);
    color: var(--secondary-color);
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 32, 78, 0.3);
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.summary-amount {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 10px 0;
}

.trade-highlight {
    background-color: #90EE90 !important;
    font-weight: 600;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
    .dashboard-title {
        font-size: 1.6rem;
    }
    .dashboard-subtitle {
        font-size: 0.9rem;
    }
    .section-header {
        font-size: 1rem;
    }
    .financial-table {
        font-size: 0.75rem;
    }
    .financial-table th, .financial-table td {
        padding: 8px 5px;
    }
    .summary-amount {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .financial-dashboard {
        padding: 10px;
    }
    .dashboard-title {
        font-size: 1.4rem;
    }
    .dashboard-subtitle {
        font-size: 0.8rem;
    }
    .section-header {
        font-size: 0.95rem;
    }
    .table-container {
        padding: 10px;
    }
    .financial-table {
        font-size: 0.7rem;
        min-width: unset;
    }
    .financial-table th, .financial-table td {
        padding: 6px 4px;
    }
    .summary-cards-grid {
        grid-template-columns: 1fr;
        padding: 0 10px;
    }
}

/* Specific adjustments for iPad Pro (and similar large tablets) */
@media (min-width: 769px) and (max-width: 1024px) {
    .financial-table {
        font-size: 0.85rem;
    }
    .financial-table th, .financial-table td {
        padding: 12px 10px;
    }
    .summary-cards-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}

@media (min-width: 1025px) {
    .financial-dashboard {
        padding: 30px 50px;
    }
    .dashboard-title {
        font-size: 2.4rem;
    }
    .dashboard-subtitle {
        font-size: 1.1rem;
    }
    .financial-table {
        font-size: 0.9rem;
    }
    .summary-cards-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

</style>';


$date = strtotime($_POST['date']);
$date_for_sql = date("Y-m-d", $date);
$newDate = date("Y-m-d", $date - (24 * 60 * 60));

// Get basic financial data
$line = $dbc->GetRecord("bs_smg_daily", "*", "date = '" . $date_for_sql . "' ORDER BY date DESC");
$cash = $line['cash'];

// Get interest rates
for ($d = 1; $d <= date("t", $date); $d++) {
    $date_sql = date("Y-m-", $date) . sprintf("%02d", $d);
    if ($dbc->HasRecord("bs_smg_rate", "date = '" . $date_sql . "'")) {
        $line = $dbc->GetRecord("bs_smg_rate", "*", "date = '" . $date_sql . "'");
        $interest_rate_short = $line['rate_short'];
        $interest_rate = $line['rate'];
    }
}

// SUPPLIER DATA QUERIES

$sql = "SELECT FORMAT(-SUM(value_usd_total),4) AS amount FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_standard = $dbc->Fetch($rst);
$line_standardUse = $dbc->Fetch($rst);

$sql = "SELECT FORMAT(-SUM(value_usd_total),4) AS amount FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='18'";
$rst = $dbc->Query($sql);
$line_jinsung = $dbc->Fetch($rst);

// Goldlin supplier data

$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='17')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='17')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='17')AS amount";
$rst = $dbc->Query($sql);
$line_goldlin_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='17')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='17')AS amount";
$rst = $dbc->Query($sql);
$line_goldlin_used = $dbc->Fetch($rst);

// DEFER AND ADJUST CALCULATIONS

// Defer calculations
$sql = "SELECT
    (SELECT COALESCE(SUM(value_adjust_type),0) FROM bs_adjust_defer WHERE date between '2024-01-01' AND '$newDate' AND supplier_id ='1') 
    +(SELECT COALESCE(SUM(defer),0) FROM bs_defer_cost WHERE date_defer between '2025-01-01' AND '$newDate' AND supplier_id ='1') AS amount";
$rst = $dbc->Query($sql);
$line_defer_previous = $dbc->Fetch($rst);

$sql = "SELECT
    (SELECT COALESCE(SUM(value_adjust_type),0) FROM bs_adjust_defer WHERE date between '2024-01-01' AND '$date_for_sql' AND supplier_id ='1') 
    +(SELECT COALESCE(SUM(defer),0) FROM bs_defer_cost WHERE date_defer between '2025-01-01' AND '$date_for_sql' AND supplier_id ='1') AS amount";
$rst = $dbc->Query($sql);
$line_defer = $dbc->Fetch($rst);

$sql = "SELECT FORMAT(SUM(defer),4) AS amount FROM bs_defer_cost WHERE date_defer = '$date_for_sql' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_defer_gain = $dbc->Fetch($rst);

// Adjust calculations
$sql = "SELECT SUM(value_profit) AS amount FROM bs_adjust_cost WHERE date_adjust between '2024-02-02' AND '$newDate' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_adjust_previous = $dbc->Fetch($rst);

$sql = "SELECT SUM(value_profit) AS amount FROM bs_adjust_cost WHERE date_adjust between '2024-02-02' AND '$date_for_sql' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_adjust = $dbc->Fetch($rst);

$sql = "SELECT SUM(value_profit) AS amount FROM bs_adjust_cost WHERE date_adjust = '$date_for_sql' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_adjust_gain = $dbc->Fetch($rst);

// STONEX CALCULATIONS

$sql = "SELECT SUM(defer) AS amount FROM bs_defer_cost WHERE date_defer = '$date_for_sql' AND supplier_id ='6'";
$rst = $dbc->Query($sql);
$line_stonex_gain = $dbc->Fetch($rst);


$sql = "SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_usd WHERE date = '$date_for_sql' AND type = 'Interest to STX'";
$rst = $dbc->Query($sql);
$line_stonex_interest = $dbc->Fetch($rst);

// Complex Stonex calculations
$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(value_edit_trade),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6')
- (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6') 
+ (SELECT COALESCE(SUM(defer),0) FROM bs_defer_cost WHERE date_defer between '2025-07-19' AND '$newDate' AND supplier_id ='6') 
+ (SELECT COALESCE(SUM(value_adjust_type),0) FROM bs_adjust_defer WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6') 
+ (SELECT COALESCE(SUM(usd),0) FROM bs_match_stx_add WHERE date between '2025-07-19' AND '$newDate' AND supplier_id ='6') 
+ (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_usd WHERE date between '2025-07-19' AND '$newDate' AND type = 'Interest to STX')
- (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx_defer')),0) AS amount FROM bs_match_data WHERE date between '2025-07-19' AND '$newDate') AS amount";
$rst = $dbc->Query($sql);
$line_stonex_after = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(value_edit_trade),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
- (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(defer),0) FROM bs_defer_cost WHERE date_defer between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(value_adjust_type),0) FROM bs_adjust_defer WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6') 
+ (SELECT COALESCE(SUM(usd),0) FROM bs_match_stx_add WHERE date between '2025-07-19' AND '$date_for_sql' AND supplier_id ='6') 
+ (SELECT COALESCE(-SUM(amount),0) AS amount FROM bs_purchase_usd WHERE date between '2025-07-19' AND '$date_for_sql' AND type = 'Interest to STX')
- (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx_defer')),0) AS amount FROM bs_match_data WHERE date between '2025-07-19' AND '$date_for_sql') AS amount";
$rst = $dbc->Query($sql);
$line_stonex_previous = $dbc->Fetch($rst);

// Additional Stonex calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(usd),0) FROM bs_match_stx_add WHERE date = '$date_for_sql' AND supplier_id ='6') AS amount";
$rst = $dbc->Query($sql);
$line_stonex_add = $dbc->Fetch($rst);

$sql = "SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx_defer')),0) AS amount FROM bs_match_data WHERE date = '$date_for_sql'";
$rst = $dbc->Query($sql);
$stx_deduct_defer = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='6')AS amount";
$rst = $dbc->Query($sql);
$line_stonex = $dbc->Fetch($rst);

// STBLC CALCULATIONS

$sql = "SELECT SUM(value_usd_total) AS amount FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='1'";
$rst = $dbc->Query($sql);
$line_stblc_add = $dbc->Fetch($rst);



$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='1')AS amount";
$rst = $dbc->Query($sql);
$line_stblc = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$date_for_sql' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$date_for_sql' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date between '2024-07-19' AND '$date_for_sql' AND supplier_id ='1')
- (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$date_for_sql' AND supplier_id ='1') AS amount";
$rst = $dbc->Query($sql);
$line_stblc_previous = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$newDate' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$newDate' AND supplier_id ='1')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date between '2024-07-19' AND '$newDate' AND supplier_id ='1')
- (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date between '2024-07-19' AND '$newDate' AND supplier_id ='1') AS amount";
$rst = $dbc->Query($sql);
$line_stblc_after = $dbc->Fetch($rst);

// OTHER SUPPLIER CALCULATIONS

// Jinsung calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='18')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='18')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='18')AS amount";
$rst = $dbc->Query($sql);
$line_jingsung_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='18')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='18')AS amount";
$rst = $dbc->Query($sql);
$line_jingsung_used = $dbc->Fetch($rst);

// CPC calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='20')
+ (SELECT COALESCE(SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='20')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='20')AS amount";
$rst = $dbc->Query($sql);
$line_cpc_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='20')
+ (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='20')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='20')AS amount";
$rst = $dbc->Query($sql);
$line_cpc_used = $dbc->Fetch($rst);

// Interest calculations
$tr_interest_jinsung = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '18'"
);

$tr_interest_deposit_jinsung = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='18'");
$tr_interest_jinsung = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='18'");

// Montreal calculations

$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='23')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='23')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='23')AS amount";
$rst = $dbc->Query($sql);
$line_montreal_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='23')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='23')AS amount";
$rst = $dbc->Query($sql);
$line_montreal_used = $dbc->Fetch($rst);

$tr_interest_montreal = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '23'"
);

$tr_interest_deposit_montreal = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='23'");
$tr_interest_montreal = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='23'");

// Asahi calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='25')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='25')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='25')AS amount";
$rst = $dbc->Query($sql);
$line_asahi_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='25')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='25')AS amount";
$rst = $dbc->Query($sql);
$line_asahi_used = $dbc->Fetch($rst);

$tr_interest_asahi = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '25'"
);

$tr_interest_deposit_asahi = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='25'");
$tr_interest_asahi = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='25'");

// Freeport calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='26')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='26')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='26')AS amount";
$rst = $dbc->Query($sql);
$line_freeport_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='26')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='26')AS amount";
$rst = $dbc->Query($sql);
$line_freeport_used = $dbc->Fetch($rst);

$tr_interest_freeport = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '26'"
);

$tr_interest_deposit_freeport = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='26'");
$tr_interest_freeport = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='26'");

// Sam supplier calculations

$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='22')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='22')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='22')AS amount";
$rst = $dbc->Query($sql);
$line_sam_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='22')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='22')AS amount";
$rst = $dbc->Query($sql);
$line_sam_used = $dbc->Fetch($rst);

$tr_interest_sam = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '22'"
);

$tr_interest_deposit_sam = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='22'");
$tr_interest_sam = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='22'");


// Uni

$sql = "SELECT (SELECT COALESCE(SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='24')
- (SELECT COALESCE(-SUM(value_adjust_trade),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='24')
+ (SELECT COALESCE(SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='24')AS amount";
$rst = $dbc->Query($sql);
$line_uni_add = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(-SUM(value_usd_total),0) FROM bs_transfers WHERE date = '$date_for_sql' AND supplier_id ='24')
+ (SELECT COALESCE(-SUM(thb),0) FROM bs_adjust_physical_adjust WHERE date = '$date_for_sql' AND supplier_id ='24')AS amount";
$rst = $dbc->Query($sql);
$line_uni_used = $dbc->Fetch($rst);

$tr_interest_uni = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.interest)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfers.supplier_id = '24'"
);

$tr_interest_deposit_uni = $dbc->GetRecord("bs_transfers", "SUM(value_usd_deposit)", "date = '$newDate' AND supplier_id ='24'");
$tr_interest_uni = $dbc->GetRecord("bs_transfers", "SUM(interest_match)", "date = '$date_for_sql' AND supplier_id ='24'");

// MKS calculations
$sql = "SELECT FORMAT(SUM(usd),4) AS amount FROM bs_match WHERE date between '2022-12-07' AND '$date_for_sql' AND supplier_id = '15'";
$rst = $dbc->Query($sql);
$line_mks = $dbc->Fetch($rst);

// Complex calculation
$sql = "SELECT 
    (SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE date BETWEEN '2025-01-01' AND '2025-01-01' AND supplier_id ='1' AND status > 0 AND type != 'defer')
    -(SELECT COALESCE(-SUM(usd),0) FROM bs_suppliers_mapping WHERE id = '1' AND date = '2025-01-01')
    +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (14,16,17,18,19,20,21,11,22,23,24,25,26) THEN amount = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE adj_supplier in (1,6) AND noted ='Normal' AND parent IS NULL AND type='physical' AND date BETWEEN '2025-01-01' AND '$date_for_sql')
    +(SELECT COALESCE(SUM(CASE WHEN adj_supplier in (1,6,14,19,21,11) THEN amount = '0' ELSE (rate_spot+rate_pmdc)*amount*32.1507 END ),0) FROM bs_purchase_spot WHERE adj_supplier in (16,17,18,20,22,23,24,25,26) AND currency ='USD' AND parent IS NULL AND (type='physical-adjust') AND noted ='Open-Adjust' AND date BETWEEN '2025-01-01' AND '$date_for_sql')
    +(SELECT COALESCE(-SUM(CASE WHEN supplier_id in (14,19,21,11) THEN value_usd_total = '0' ELSE value_usd_total END ),0) FROM bs_transfers WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in (1,6,16,17,18,20,22,23,24,25,26) )
    +(SELECT COALESCE(SUM(CASE WHEN supplier_id in (14,19,21,11) THEN value_usd_deposit = '0' ELSE value_usd_deposit END ),0) FROM bs_transfers WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in (1,16,17,18,20,22,23,24,25,26))
    +(SELECT COALESCE(-SUM(CASE WHEN supplier_id in (14,19,21,11) THEN value_adjust_trade = '0' ELSE value_adjust_trade END ),0) FROM bs_transfers WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in (1,6,16,17,18,20,22,23,24,25,26))
    +(SELECT COALESCE(SUM(defer),0) FROM bs_defer_cost WHERE date_defer BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id = 1)
    +(SELECT COALESCE(SUM(usd),0) FROM bs_match_deposit WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in(1,6,18,22,23,24,25,26))
    -(SELECT COALESCE(-SUM(usd),0) FROM bs_match WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in(1,18,22,23,24,25,26))
    -(SELECT COALESCE(SUM(usd),0) FROM bs_match_fx WHERE date BETWEEN '2025-01-01' AND '$date_for_sql' AND supplier_id in(6,18,22,23,24,25,26))
    +(SELECT COALESCE(-SUM((rate_spot)*amount*32.1507),0) FROM bs_sales_spot WHERE supplier_id = 1 AND type='physical' AND status = 1 AND value_date BETWEEN '2024-01-10' AND '$date_for_sql' )
    +(SELECT COALESCE(SUM((rate_spot)*amount*32.1507),0) FROM bs_purchase_spot WHERE supplier_id = 1 AND parent IS NULL AND type='physical' AND noted ='Close-Adjust' AND date BETWEEN '2024-01-10' AND '$date_for_sql')
    +(SELECT COALESCE(SUM(value_profit),0) FROM bs_adjust_cost WHERE date_adjust BETWEEN '2024-01-10' AND '$date_for_sql' AND supplier_id =1)";
$rst = $dbc->Query($sql);
$line_aa = $dbc->Fetch($rst);

// USD BANK CALCULATIONS

// USD Add calculations
$sql = "SELECT SUM(usd) AS amount FROM bs_match_usd WHERE date = '$date_for_sql' AND bank ='3'";
$rst = $dbc->Query($sql);
$line_usd_add_bbl = $dbc->Fetch($rst);

$sql = "SELECT SUM(usd) AS amount FROM bs_match_usd WHERE date = '$date_for_sql' AND bank ='9'";
$rst = $dbc->Query($sql);
$line_usd_add_scb = $dbc->Fetch($rst);

$sql = "SELECT SUM(usd) AS amount FROM bs_match_usd WHERE date = '$date_for_sql' AND bank ='7'";
$rst = $dbc->Query($sql);
$line_usd_add_kbank = $dbc->Fetch($rst);

// USD Usage calculations
$tr_used_scb_usd = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'SCB'"
);

$tr_used_bbl_usd = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BBL'"
);

$tr_used_kbank_usd = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'KBANK'"
);
// After period calculations
$sql = "SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$newDate' AND bank ='3') AS amount";
$rst = $dbc->Query($sql);
$line_usd_after_bbl = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(usd)+12.11,0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$newDate' AND bank ='9') AS amount";
$rst = $dbc->Query($sql);
$line_usd_after_scb = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$newDate' AND bank ='7') AS amount";
$rst = $dbc->Query($sql);
$line_usd_after_kbank = $dbc->Fetch($rst);

// Used after calculations
$tr_used_bbl_usd_after = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $newDate . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BBL'"
);

$tr_used_scb_usd_after = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $newDate . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'SCB'"
);


$tr_used_kbank_usd_after = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $newDate . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'KBANK'"
);

// Previous period calculations
$sql = "SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$date_for_sql' AND bank ='3') AS amount";
$rst = $dbc->Query($sql);
$line_usd_previous_bbl = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(usd)+12.11,0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$date_for_sql' AND bank ='9') AS amount";
$rst = $dbc->Query($sql);
$line_usd_previous_scb = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(usd),0) FROM bs_match_usd WHERE date between '2024-10-19' AND '$date_for_sql' AND bank ='7') AS amount";
$rst = $dbc->Query($sql);
$line_usd_previous_kbank = $dbc->Fetch($rst);


$tr_used_bbl_usd_previous = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BBL'"
);

$tr_used_scb_usd_previous = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'SCB'"
);

$tr_used_kbank_usd_previous = $dbc->GetRecord(
    "bs_transfer_payments 
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date between '2024-11-01' AND '" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'KBANK'"
);
// STX Adjust calculations
$sql = "SELECT (SELECT COALESCE(SUM(value_profit),0) AS amount FROM bs_adjust_cost WHERE date_adjust between '2025-01-01' AND '$newDate' AND supplier_id ='6') 
+ (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.rollover_stx')),0) AS amount FROM bs_match_data WHERE date between '2025-01-01' AND '$newDate') 
- (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx')),0) AS amount FROM bs_match_data WHERE date between '2025-01-01' AND '$newDate') AS amount";
$rst = $dbc->Query($sql);
$stx_adjust_gain_before = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(value_profit),0) AS amount FROM bs_adjust_cost WHERE date_adjust= '$date_for_sql' AND supplier_id ='6') AS amount";

$rst = $dbc->Query($sql);
$stx_adjust_gain = $dbc->Fetch($rst);

$sql = "SELECT (SELECT COALESCE(SUM(value_profit),0) AS amount FROM bs_adjust_cost WHERE date_adjust between '2025-01-01' AND '$date_for_sql' AND supplier_id ='6')
+ (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.rollover_stx')),0) AS amount FROM bs_match_data WHERE date between '2025-01-01' AND '$date_for_sql') 
- (SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx')),0) AS amount FROM bs_match_data WHERE date between '2025-01-01' AND '$date_for_sql') AS amount";
$rst = $dbc->Query($sql);
$stx_adjust_gain_after = $dbc->Fetch($rst);

$sql = "SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.rollover_stx')),0) AS amount FROM bs_match_data WHERE date = '$date_for_sql'";
$rst = $dbc->Query($sql);
$stx_rollover = $dbc->Fetch($rst);

$sql = "SELECT COALESCE(SUM(JSON_EXTRACT(data, '$.deduct_stx')),0) AS amount FROM bs_match_data WHERE date = '$date_for_sql'";
$rst = $dbc->Query($sql);
$stx_deduct = $dbc->Fetch($rst);

// =============================================================================
// BANK FINANCE CALCULATIONS
// =============================================================================

$aBalUSDFinance = array(0, 0, 0, 0);

$sql_previous_date = "BETWEEN '2025-01-01' AND '" . date("Y-m-d", $date - 86400) . "'";
$sql_only_physical = " AND type = 'Physical' AND status = 1";

$previous_amount_bbl = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)", "date $sql_previous_date AND bank LIKE 'BBL'");
$previous_amount_scb = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)", "date $sql_previous_date AND bank LIKE 'SCB'");
$previous_amount_kbank = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)", "date $sql_previous_date AND bank LIKE 'KBANK'");
$previous_amount_bay = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed-paid_usd)", "date $sql_previous_date AND bank LIKE 'BAY'");

$previous_tr_used_bbl = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BBL'"
);

$previous_tr_used_scb = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'SCB'"
);

$previous_tr_used_scb_1 = $dbc->GetRecord(
    "bs_transfer_payments
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfers.date $sql_previous_date AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'SCB'"
);

$previous_tr_used_scb_2 = $dbc->GetRecord(
    "bs_transfer_payments
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date $sql_previous_date AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'SCB'"
);

$previous_tr_used_kbank = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'KBANK'"
);

$previous_tr_used_bbl_1 = $dbc->GetRecord(
    "bs_transfer_payments
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfers.date $sql_previous_date AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BBL'"
);

$previous_tr_used_bbl_2 = $dbc->GetRecord(
    "bs_transfer_payments
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date $sql_previous_date AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BBL'"
);

$previous_tr_used_bay = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BAY'"
);

$previous_tr_used_bay_1 = $dbc->GetRecord(
    "bs_match_tr
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_match_tr.transfer_id",
    "SUM(bs_match_tr.paid)",
    "bs_match_tr.date $sql_previous_date AND bs_transfers.bank LIKE 'BAY'"
);

$aBalUSDFinance[0] += $previous_amount_scb[0] - $previous_tr_used_scb[0] + $previous_tr_used_scb_1[0] - $previous_tr_used_scb_2[0];
$aBalUSDFinance[1] += $previous_amount_kbank[0] - $previous_tr_used_kbank[0];
$aBalUSDFinance[2] += $previous_amount_bbl[0] - $previous_tr_used_bbl[0] + $previous_tr_used_bbl_1[0] - $previous_tr_used_bbl_2[0];
$aBalUSDFinance[3] += $previous_amount_bay[0] - $previous_tr_used_bay[0] + $previous_tr_used_bay_1[0];

// Current day finance calculations
$amount_scb = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed)", "date ='" . $date_for_sql . "' AND bank LIKE 'SCB'");
$amount_kbank = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed)", "date ='" . $date_for_sql . "' AND bank LIKE 'KBANK'");
$amount_bbl = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed)", "date ='" . $date_for_sql . "' AND bank LIKE 'BBL'");
$amount_bay = $dbc->GetRecord("bs_transfers", "SUM(value_usd_fixed+value_usd_nonfixed)", "date ='" . $date_for_sql . "' AND bank LIKE 'BAY'");

$tr_used_bbl = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'BBL'"
);

$tr_used_scb = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'SCB'"
);

$tr_used_bay = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'BAY'"
);

$tr_used_kbank = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'KBANK'"
);

$tr_used_bay_usd = $dbc->GetRecord(
    "bs_transfer_payments
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_payments.transfer_id",
    "SUM(bs_transfer_payments.paid)",
    "bs_transfer_payments.date ='" . $date_for_sql . "' AND bs_transfer_payments.currency ='USD' AND bs_transfers.bank LIKE 'BAY'"
);

$get_total = $line_aa[0] - $line_stblc_previous['amount'] - $line_defer['amount'] - $line_adjust['amount'] - $line_stonex_previous['amount'] + $aBalUSDFinance[0] + $amount_scb[0] - $tr_used_scb[0] - $tr_used_scb_usd[0] + $aBalUSDFinance[1] + $amount_kbank[0] - $tr_used_kbank[0] + $aBalUSDFinance[2] + $amount_bbl[0] - $tr_used_bbl[0] + $aBalUSDFinance[3] + $amount_bay[0] - $tr_used_bay[0];

// =============================================================================
// UNUSED FORWARD CONTRACTS CALCULATIONS
// =============================================================================

$aBalUSD = array(452123.02, 0, 0, 0);
$aBalTHB = array(15441601.13, 0, 0, 0);
$aBalPremium = array(0, 0, 0, 0);

$sql_previous_date = "BETWEEN '2024-01-01' AND '" . date("Y-m-d", $date - 86400) . "'";
$sql_only_month = "BETWEEN '" . date("Y-m-d", strtotime(date("Y-m-01", $date)) - 86400) . "' AND '" . date("Y-m-d", $date - 86400) . "'";
$sql_only_physical_un = " AND type != 'Stock' AND type != 'Fee' AND status = 1 AND id NOT IN ( 3183,3182 )";

$previous_amount_bbl_un = $dbc->GetRecord(
    "bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_finance)",
    "date $sql_previous_date AND bank LIKE 'BBL' $sql_only_physical_un"
);

$previous_amount_scb_un = $dbc->GetRecord(
    "bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_finance)",
    "date $sql_previous_date AND bank LIKE 'SCB' $sql_only_physical_un"
);

$previous_amount_kbank_un = $dbc->GetRecord(
    "bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_finance)",
    "date $sql_previous_date AND bank LIKE 'KBANK' $sql_only_physical_un"
);

$previous_amount_bay_un = $dbc->GetRecord(
    "bs_purchase_usd",
    "SUM(amount),SUM(amount*rate_finance)",
    "date $sql_previous_date AND bank LIKE 'BAY' $sql_only_physical_un"
);

// Interest calculations by bank
$scb_interest = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type LIKE '%STX%' ");
$scb_interest_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type LIKE '%STD%' ");
$scb_trade = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type = 'Trade' ");

$kbank_interest = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type LIKE '%STX%' ");
$kbank_interest_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type LIKE '%STD%' ");
$kbank_trade = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type = 'Trade' ");

$bbl_interest = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type LIKE '%STX%' ");
$bbl_interest_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type LIKE '%STD%' ");
$bbl_trade = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type = 'Trade' ");

$bay_interest = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type LIKE '%STX%' ");
$bay_interest_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type LIKE '%STD%' ");
$bay_trade = $dbc->GetRecord("bs_purchase_usd", "SUM(amount)", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type = 'Trade' ");

$previous_tr_used_bbl_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BBL'"
);

$previous_tr_used_scb_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'SCB'"
);

$previous_tr_used_kbank_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'KBANK'"
);

$previous_tr_used_bay_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance)",
    "bs_transfer_usd.date $sql_previous_date AND bs_transfers.bank LIKE 'BAY'"
);

$aBalUSD[0] += $previous_amount_bbl_un[0] - $previous_tr_used_bbl_un[0];
$aBalUSD[1] += $previous_amount_scb_un[0] - $previous_tr_used_scb_un[0];
$aBalUSD[2] += $previous_amount_kbank_un[0] - $previous_tr_used_kbank_un[0];
$aBalUSD[3] += $previous_amount_bay_un[0] - $previous_tr_used_bay_un[0];

$aBalTHB[0] += $previous_amount_bbl_un[1] - $previous_tr_used_bbl_un[1];
$aBalTHB[1] += $previous_amount_scb_un[1] - $previous_tr_used_scb_un[1];
$aBalTHB[2] += $previous_amount_kbank_un[1] - $previous_tr_used_kbank_un[1];
$aBalTHB[3] += $previous_amount_bay_un[1] - $previous_tr_used_bay_un[1];

// Premium Balance
$tr_premium_bbl_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date " . $sql_only_month . " AND bs_transfers.bank LIKE 'BBL'"
);

$tr_premium_scb_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date " . $sql_only_month . " AND bs_transfers.bank LIKE 'SCB'"
);

$tr_premium_bay_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date " . $sql_only_month . " AND bs_transfers.bank LIKE 'BAY'"
);

$tr_premium_kbank_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date " . $sql_only_month . " AND bs_transfers.bank LIKE 'KBANK'"
);

$aBalPremium[0] = $tr_premium_bbl_un[0];
$aBalPremium[1] = $tr_premium_scb_un[0];
$aBalPremium[2] = $tr_premium_kbank_un[0];
$aBalPremium[3] = $tr_premium_bay_un[0];

$amount_bbl_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_finance),SUM(rate_finance)", "date ='" . $date_for_sql . "' AND bank LIKE 'BBL' $sql_only_physical_un");
$amount_scb_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_finance),SUM(rate_finance)", "date ='" . $date_for_sql . "' AND bank LIKE 'SCB' $sql_only_physical_un");
$amount_kbank_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_finance),SUM(rate_finance)", "date ='" . $date_for_sql . "' AND bank LIKE 'KBANK' $sql_only_physical_un");
$amount_bay_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_finance),SUM(rate_finance)", "date ='" . $date_for_sql . "' AND bank LIKE 'BAY' $sql_only_physical_un");

$amount_bbl_diff = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_exchange),SUM(rate_exchange)", "date ='" . $date_for_sql . "' AND bank LIKE 'BBL' $sql_only_physical_un");
$amount_scb_diff = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_exchange),SUM(rate_exchange)", "date ='" . $date_for_sql . "' AND bank LIKE 'SCB' $sql_only_physical_un");
$amount_kbank_diff = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_exchange),SUM(rate_exchange)", "date ='" . $date_for_sql . "' AND bank LIKE 'KBANK' $sql_only_physical_un");
$amount_bay_diff = $dbc->GetRecord("bs_purchase_usd", "SUM(amount),SUM(amount*rate_exchange),SUM(rate_exchange)", "date ='" . $date_for_sql . "' AND bank LIKE 'BAY' $sql_only_physical_un");

$scb_interest_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type LIKE '%STX%' ");
$scb_interest_un_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type LIKE '%STD%' ");
$scb_trade_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'SCB' AND type = 'Trade' ");

$kbank_interest_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type LIKE '%STX%' ");
$kbank_interest_un_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type LIKE '%STD%' ");
$kbank_trade_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'KBANK' AND type = 'Trade' ");

$bbl_interest_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type LIKE '%STX%' ");
$bbl_interest_un_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type LIKE '%STD%' ");
$bbl_trade_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BBL' AND type = 'Trade' ");

$bay_interest_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type LIKE '%STX%' ");
$bay_interest_un_xx = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type LIKE '%STD%' ");
$bay_trade_un = $dbc->GetRecord("bs_purchase_usd", "SUM(amount*rate_finance) AS amount", " date = '$date_for_sql' AND bank LIKE 'BAY' AND type = 'Trade' ");

$tr_used_bbl_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'BBL'"
);

$tr_used_scb_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'SCB'"
);

$tr_used_kbank_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'KBANK'"
);

$tr_used_bay_un = $dbc->GetRecord(
    "bs_transfer_usd
    LEFT JOIN bs_transfers ON bs_transfers.id = bs_transfer_usd.transfer_id
    LEFT JOIN bs_purchase_usd ON bs_purchase_usd.id = bs_transfer_usd.purchase_id",
    "SUM(bs_purchase_usd.amount),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance),
    SUM(bs_purchase_usd.amount*bs_purchase_usd.rate_finance+bs_transfer_usd.premium),
    SUM(bs_transfer_usd.premium)",
    "bs_transfer_usd.date ='" . $date_for_sql . "' AND bs_transfers.bank LIKE 'BAY'"
);

$allbank_total = 0;

// =============================================================================
// DASHBOARD OUTPUT
// =============================================================================

echo '<div class="financial-dashboard">';


// =============================================================================
// FCD - USD SECTION
// =============================================================================

echo '<div class="report-section">';
echo '<div class="section-header">FCD - USD Position</div>';
echo '<div class="table-container">';
echo '<table class="financial-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Account Type</th>';
echo '<th>Bank</th>';
echo '<th>B/F</th>';
echo '<th colspan="5"></th>';
echo '<th>Add</th>';
echo '<th>Used Today</th>';
echo '<th colspan="2"></th>';
echo '<th>C/F</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">FCD - USD</td>';
echo '<td colspan="11"></td>';
echo '<td class="amount-cell"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">BBL</td>';
echo '<td class="amount-cell">' . number_format($line_usd_after_bbl['amount'] - $tr_used_bbl_usd_after['0'], 4) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_add_bbl['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format(-$tr_used_bbl_usd['0'], 4) . '</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_previous_bbl['amount'] - $tr_used_bbl_usd_previous['0'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">SCB</td>';
echo '<td class="amount-cell">' . number_format($line_usd_after_scb['amount'] - $tr_used_scb_usd_after['0'], 4) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_add_scb['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format(-$tr_used_scb_usd['0'], 4) . '</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_previous_scb['amount'] - $tr_used_scb_usd_previous['0'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">KBANK</td>';
echo '<td class="amount-cell">' . number_format($line_usd_after_kbank['amount'] - $tr_used_kbank_usd_after['0'], 4) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_add_kbank['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format(-$tr_used_kbank_usd['0'], 4) . '</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell">' . number_format($line_usd_previous_kbank['amount'] - $tr_used_kbank_usd_previous['0'], 4) . '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

// =============================================================================
// OUTSTANDING USD / DEPOSIT SECTION
// =============================================================================

echo '<div class="report-section">';
echo '<div class="section-header">Outstanding USD / Deposit Position</div>';
echo '<div class="table-container">';
echo '<table class="financial-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Category</th>';
echo '<th>Supplier/Bank</th>';
echo '<th>B/F</th>';
echo '<th>LC</th>';
echo '<th>Interest</th>';
echo '<th>Charges</th>';
echo '<th>Gain (Loss) From Trade</th>';
echo '<th>Deposit</th>';
echo '<th>Add</th>';
echo '<th>Used Today</th>';
echo '<th>Rollover</th>';
echo '<th>Deduct</th>';
echo '<th>C/F</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">Require FX before deduct deposit</td>';
echo '<td colspan="11"></td>';
echo '<td class="amount-cell total-row">' . number_format($line_aa[0], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="row-header">Outstanding USD / Deposit</td>';
echo '<td colspan="12"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STX (Defer) กำไร/(ขาดทุน) ที่ STX</td>';
echo '<td class="amount-cell">' . number_format($line_stonex_after['amount'], 4) . '</td>';
echo '<td></td>';
echo '<td class="amount-cell">' . number_format($line_stonex_interest['amount'], 4) . '</td>';
echo '<td></td>';
echo '<td class="amount-cell">' . number_format($line_stonex_gain['amount'], 4) . '</td>';
echo '<td class="amount-cell"></td>';
echo '<td class="amount-cell">' . number_format($line_stonex_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_stonex['amount'], 4) . '</td>';
echo '<td></td>';
echo '<td class="amount-cell">' . number_format($stx_deduct_defer['amount'], 4) . '</td>';
echo '<td class="amount-cell">' . number_format($line_stonex_previous['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STX (Fund in) (Defer)</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="9"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STX (Trade)</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="9"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STX (Adjust Cost) กำไร/(ขาดทุน) ที่ STX</td>';
echo '<td class="amount-cell">' . number_format($stx_adjust_gain_before['amount'], 4) . '</td>';
echo '<td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($stx_adjust_gain['amount'], 4) . '</td>';
echo '<td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($stx_rollover['amount'], 4) . '</td>';
echo '<td class="amount-cell">' . number_format($stx_deduct['amount'], 4) . '</td>';
echo '<td class="amount-cell">' . number_format($stx_adjust_gain_after['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">HERAEUS</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="9"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">Local Bar</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="9"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">MKS HONGKONG</td>';
echo '<td class="amount-cell">' . $line_mks['amount'] . '</td>';
echo '<td colspan="9"></td>';
echo '<td class="amount-cell">' . $line_mks['amount'] . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">Gold Linkage</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="5"></td>';
echo '<td class="amount-cell">' . $line_goldlin_add['amount'] . '</td>';
echo '<td class="amount-cell text-negative">' . $line_goldlin_used['amount'] . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STD (STBLC)</td>';
echo '<td class="amount-cell">' . number_format($line_stblc_after['amount'], 4) . '</td>';
echo '<td colspan="4"></td>';
echo '<td class="amount-cell"></td>';
echo '<td class="amount-cell">' . number_format($line_stblc_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_stblc['amount'], 4) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative">' . number_format($line_stblc_previous['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STD (Physical)เงินเกินที่ STD</td>';
echo '<td class="amount-cell">' . number_format($line_defer_previous['amount'], 4) . '</td>';
echo '<td></td>';
if ($cash < 0) {
    echo '<td class="amount-cell text-negative">' .
        number_format($cash * ($interest_rate_short / 100) / 360, 2) .
        '</td>';
} else {
    echo '<td class="amount-cell">' .
        number_format($cash * ($interest_rate / 100) / 360, 2) .
        '</td>';
}
echo '<td></td>';
echo '<td class="amount-cell">' . $line_defer_gain['amount'] . '</td>';
echo '<td colspan="5"></td>';
echo '<td class="amount-cell text-negative">' . number_format($line_defer['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STD (Fund in) (Defer)</td>';
echo '<td colspan="12"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STD (Trade)</td>';
echo '<td colspan="12"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">STD (Adjust Cost) กำไร/(ขาดทุน) ที่ STd</td>';
echo '<td class="amount-cell">' . number_format($line_adjust_previous['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '<td class="amount-cell">' . number_format($line_adjust_gain['amount'], 4) . '</td>';
echo '<td colspan="5"></td>';
echo '<td class="amount-cell text-negative">' . number_format($line_adjust['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">JINSUNG</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_jinsung[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_jingsung_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_jingsung_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">SAM</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_sam[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_sam_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_sam_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">MONTREAL</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_montreal[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_montreal_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_montreal_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">UNIPRECIOUS</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_uni[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_uni_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_uni_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">CPC RESOURCES LIMITED</td>';
echo '<td class="amount-cell">-</td>';
echo '<td colspan="5"></td>';
echo '<td class="amount-cell">' . number_format($line_cpc_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_cpc_used['amount'], 4) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell">-</td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">ASAHI</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_asahi[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_asahi_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_asahi_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">FREEPORT</td>';
echo '<td></td><td></td>';
echo '<td class="amount-cell text-negative">' . number_format($tr_interest_freeport[0], 2) . '</td>';
echo '<td colspan="2"></td>';
echo '<td class="amount-cell text-negative"></td>';
echo '<td class="amount-cell">' . number_format($line_freeport_add['amount'], 4) . '</td>';
echo '<td class="amount-cell text-negative">' . number_format($line_freeport_used['amount'], 4) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';


echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

// =============================================================================
// REQUIRE FX AFTER DEDUCT DEPOSIT SECTION
// =============================================================================

echo '<div class="report-section">';
echo '<div class="section-header">Require FX After Deduct Deposit</div>';
echo '<div class="table-container">';
echo '<table class="financial-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Category</th>';
echo '<th>Bank</th>';
echo '<th>B/F</th>';
echo '<th>LC</th>';
echo '<th>Interest</th>';
echo '<th>Charges</th>';
echo '<th>Gain (Loss) From Trade</th>';
echo '<th>Deposit</th>';
echo '<th>Add</th>';
echo '<th>Used Today</th>';
echo '<th>USD In</th>';
echo '<th>Deduct Deferred Stock Mkt Value</th>';
echo '<th>C/F</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">Require FX after deduct deposit</td>';
echo '<td colspan="11"></td>';
echo '<td class="amount-cell total-row">' . number_format($line_aa[0] - $line_stblc_previous['amount'] - $line_defer['amount'] - $line_adjust['amount'] - $line_stonex_previous['amount'], 4) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="row-header">Open Non Fix</td>';
echo '<td class="sub-header">SCB</td>';
echo '<td class="amount-cell">' . number_format($aBalUSDFinance[0], 2) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($amount_scb[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_scb[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_scb_usd[0], 2) . '</td>';
echo '<td></td>';
$remain = $aBalUSDFinance[0] + $amount_scb[0] - $tr_used_scb[0] - $tr_used_scb_usd[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="row-header">Open Non Fix</td>';
echo '<td class="sub-header">KBANK</td>';
echo '<td class="amount-cell">' . number_format($aBalUSDFinance[1], 2) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($amount_kbank[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_kbank[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_kbank_usd[0], 2) . '</td>';
echo '<td></td>';
$remain = $aBalUSDFinance[1] + $amount_kbank[0] - $tr_used_kbank[0] - $tr_used_kbank_usd[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="row-header">Open Non Fix</td>';
echo '<td class="sub-header">BBL</td>';
echo '<td class="amount-cell">' . number_format($aBalUSDFinance[2], 2) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($amount_bbl[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bbl[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bbl_usd[0], 2) . '</td>';
echo '<td></td>';
$remain = $aBalUSDFinance[2] + $amount_bbl[0] - $tr_used_bbl[0] - $tr_used_bbl_usd[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td class="row-header">Open Non Fix</td>';
echo '<td class="sub-header">BAY</td>';
echo '<td class="amount-cell">' . number_format($aBalUSDFinance[3], 2) . '</td>';
echo '<td></td><td></td><td></td><td></td><td></td>';
echo '<td class="amount-cell">' . number_format($amount_bay[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bay[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bay_usd[0], 2) . '</td>';
echo '<td></td>';
$remain = $aBalUSDFinance[3] + $amount_bay[0] - $tr_used_bay[0] - $tr_used_bay_usd[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '</tr>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">Total require FX amount</td>';
echo '<td colspan="11"></td>';
echo '<td class="amount-cell total-row">' . number_format($get_total, 4) . '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

// UNUSED FORWARD CONTRACTS SECTION

echo '<div class="report-section">';
echo '<div class="section-header">Unused Forward Contracts</div>';
echo '<div class="table-container">';
echo '<table class="financial-table">';
echo '<thead>';
echo '<tr>';
echo '<th rowspan="2">Category</th>';
echo '<th rowspan="2">Bank</th>';
echo '<th rowspan="2">B/F</th>';
echo '<th class="trade-highlight">Trade</th>';
echo '<th rowspan="2">+ Add</th>';
echo '<th class="trade-highlight">Interest</th>';
echo '<th rowspan="2">- Used Today</th>';
echo '<th rowspan="2">Gain Loss Exchange Rate</th>';
echo '<th rowspan="2">C/F</th>';
echo '<th rowspan="2">Beginning Interest / Premium</th>';
echo '<th rowspan="2">Total Bank</th>';
echo '<th rowspan="2">Interest /Premium</th>';
echo '<th rowspan="2">Interest / Premium(Monthly)</th>';
echo '<th rowspan="2"></th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

echo '<tr>';
echo '<td class="row-header">Unused Forward Contracts</td>';
echo '<td colspan="13"></td>';
echo '</tr>';

// USD Section
echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">SCB</td>';
echo '<td class="amount-cell">' . number_format($aBalUSD[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($scb_trade[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_scb_un[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($scb_interest[0] + $scb_interest_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_scb_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_scb_diff[2] - $amount_scb_un[2], 2) . '</td>';
$remain = $aBalUSD[1] + $amount_scb_un[0] - $tr_used_scb_un[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
$allbank_total += $remain;
echo '<td colspan="5"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">KBANK</td>';
echo '<td class="amount-cell">' . number_format($aBalUSD[2], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($kbank_trade[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_kbank_un[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($kbank_interest[0] + $kbank_interest_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_kbank_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_kbank_diff[2] - $amount_kbank_un[2], 2) . '</td>';
$remain = $aBalUSD[2] + $amount_kbank_un[0] - $tr_used_kbank_un[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
$allbank_total += $remain;
echo '<td colspan="5"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">BBL</td>';
echo '<td class="amount-cell">' . number_format($aBalUSD[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bbl_trade[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bbl_un[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bbl_interest[0] + $bbl_interest_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bbl_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bbl_diff[2] - $amount_bbl_un[2], 2) . '</td>';
$remain = $aBalUSD[0] + $amount_bbl_un[0] - $tr_used_bbl_un[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
$allbank_total += $remain;
echo '<td colspan="5"></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">BAY</td>';
echo '<td class="amount-cell">' . number_format($aBalUSD[3], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bay_trade[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bay_un[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bay_interest[0] + $bay_interest_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bay_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bay_diff[2] - $amount_bay_un[2], 2) . '</td>';
$remain = $aBalUSD[3] + $amount_bay_un[0] - $tr_used_bay_un[0];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
$allbank_total += $remain;
echo '<td colspan="5"></td>';
echo '</tr>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">Total require FX amount</td>';
echo '<td colspan="7"></td>';
echo '<td class="amount-cell total-row">' . number_format(($get_total) - $allbank_total, 4) . '</td>';
echo '<td colspan="5"></td>';
echo '</tr>';

echo '<tr class="highlight-warning">';
echo '<td class="row-header">Total require FX amount incl. LC</td>';
echo '<td colspan="7"></td>';
echo '<td class="amount-cell total-row">' . number_format(($get_total) - $allbank_total, 4) . '</td>';
echo '<td colspan="5"></td>';
echo '</tr>';

// THB Section
echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">SCB</td>';
echo '<td class="amount-cell">' . number_format($aBalTHB[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($scb_trade_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_scb_un[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($scb_interest_un[0] + $scb_interest_un_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_scb_un[1], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_scb_diff[1] - $amount_scb_un[1], 2) . '</td>';
$remain = $aBalTHB[1] + $amount_scb_un[1] - $tr_used_scb_un[1];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[1], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_scb_un[2], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_scb_un[3], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[1] - $tr_used_scb_un[3], 2) . '</td>';
echo '<td></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">KBANK</td>';
echo '<td class="amount-cell">' . number_format($aBalTHB[2], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($kbank_trade_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_kbank_un[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($kbank_interest_un[0] + $kbank_interest_un_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_kbank_un[1], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_kbank_diff[1] - $amount_kbank_un[1], 2) . '</td>';
$remain = $aBalTHB[2] + $amount_kbank_un[1] - $tr_used_kbank_un[1];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[2], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_kbank_un[2], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_kbank_un[3], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[2] - $tr_used_kbank_un[3], 2) . '</td>';
echo '<td></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">BBL</td>';
echo '<td class="amount-cell">' . number_format($aBalTHB[0], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bbl_trade_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bbl_un[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bbl_interest_un[0] + $bbl_interest_un_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bbl_un[1], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bbl_diff[1] - $amount_bbl_un[1], 2) . '</td>';
$remain = $aBalTHB[0] + $amount_bbl_un[1] - $tr_used_bbl_un[1];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_bbl_un[2], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_bbl_un[3], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[0] - $tr_used_bbl_un[3], 2) . '</td>';
echo '<td></td>';
echo '</tr>';

echo '<tr>';
echo '<td></td>';
echo '<td class="sub-header">BAY</td>';
echo '<td class="amount-cell">' . number_format($aBalTHB[3], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bay_trade_un[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bay_un[1], 2) . '</td>';
echo '<td class="amount-cell trade-highlight">' . number_format($bay_interest_un[0] + $bay_interest_un_xx[0], 2) . '</td>';
echo '<td class="amount-cell">' . number_format(-$tr_used_bay_un[1], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($amount_bay_diff[1] - $amount_bay_un[1], 2) . '</td>';
$remain = $aBalTHB[3] + $amount_bay_un[1] - $tr_used_bay_un[1];
echo '<td class="amount-cell">' . number_format($remain, 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[3], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_bay_un[2], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($tr_used_bay_un[3], 2) . '</td>';
echo '<td class="amount-cell">' . number_format($aBalPremium[3] - $tr_used_bay_un[3], 2) . '</td>';
echo '<td></td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '</div>';

// SUMMARY SECTION

echo '<div class="report-section">';
echo '<div class="section-header">Financial Summary</div>';
echo '<div class="table-container">';
echo '<div class="row">';

echo '<div class="col-md-4">';
echo '<div class="summary-card">';
echo '<h6>Total FX Requirement</h6>';
echo '<div class="summary-amount">' . number_format($get_total, 4) . '</div>';
echo '<small>USD Amount</small>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-4">';
echo '<div class="summary-card">';
echo '<h6>Total Forward Contracts</h6>';
echo '<div class="summary-amount">' . number_format($allbank_total, 4) . '</div>';
echo '<small>USD Amount</small>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-4">';
echo '<div class="summary-card">';
echo '<h6>Net FX Position</h6>';
echo '<div class="summary-amount ' . (($get_total - $allbank_total) < 0 ? 'text-positive' : 'text-negative') . '">' . number_format($get_total - $allbank_total, 2) . '</div>';
echo '<small>USD Amount</small>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';


echo '</div>';
