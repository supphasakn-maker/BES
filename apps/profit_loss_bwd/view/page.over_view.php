<?php
global $dbc;
?>

<style>
    .trader-section {
        background: linear-gradient(135deg, #00204E 0%, #003875 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 32, 78, 0.3);
    }

    .trader-header {
        text-align: center;
        margin-bottom: 2rem;
        color: white;
    }

    .trader-header h4 {
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .trader-header p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        margin: 0;
    }

    .balance-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 32, 78, 0.15);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .balance-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #00204E 0%, #004A9F 100%);
    }

    .balance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 32, 78, 0.25);
    }

    .balance-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .balance-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #00204E 0%, #004A9F 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.2rem;
        box-shadow: 0 4px 15px rgba(0, 32, 78, 0.3);
    }

    .balance-value {
        flex: 1;
    }

    .balance-amount {
        font-size: 1.8rem;
        font-weight: 700;
        color: #00204E;
        margin: 0;
        line-height: 1.2;
    }

    .balance-amount.negative {
        color: #dc3545;
    }

    .balance-amount.positive {
        color: #28a745;
    }

    .balance-rate {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .balance-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #00204E;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .balance-title .title-icon {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
        font-size: 0.8rem;
        color: white;
    }

    .title-icon.silver {
        background: #28a745;
    }

    .title-icon.fx {
        background: #dc3545;
    }

    .title-icon.profit {
        background: #ffc107;
        color: #000 !important;
    }

    .balance-subtitle {
        font-size: 0.85rem;
        color: #6c757d;
        margin: 0;
        line-height: 1.4;
    }

    .balance-date {
        font-size: 0.8rem;
        color: #adb5bd;
        font-style: italic;
    }

    .summary-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 8px 25px rgba(0, 32, 78, 0.15);
        border-left: 4px solid #00204E;
    }

    .summary-section .summary-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #00204E 0%, #004A9F 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 6px 20px rgba(0, 32, 78, 0.3);
    }

    .summary-text {
        color: #00204E;
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
    }

    .summary-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .trader-section {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .balance-card {
            padding: 1.5rem;
        }

        .balance-amount {
            font-size: 1.6rem;
        }

        .balance-header {
            flex-direction: column;
            text-align: center;
        }

        .balance-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }
    }
</style>

<div class="trader-section">
    <div class="trader-header">
        <h4>Overview</h4>
        <p>ภาพรวมยอดคงเหลือและกำไรขาดทุนล่าสุด</p>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-4 mb-3">
            <?php
            $sql_latest_date = "(SELECT MAX(mapped) AS latest_date FROM bs_mapping_profit_sumusd_bwd)
                            UNION
                            (SELECT MAX(mapped) AS latest_date FROM bs_mapping_profit_bwd)
                            UNION
                            (SELECT MAX(value_date) AS latest_date FROM bs_purchase_usd_profit_bwd)
                            UNION
                            (SELECT MAX(value_date) AS latest_date FROM bs_purchase_spot_profit_bwd)
                            ORDER BY latest_date DESC
                            LIMIT 1";
            $rst_latest_date = $dbc->Query($sql_latest_date);
            $latest_date_row = $dbc->Fetch($rst_latest_date);
            $latest_report_date = $latest_date_row['latest_date'];

            $totalordersusd = 0;
            $totalordersthb = 0;
            $usd_true = 0;
            $thb_true = 0;
            $latest_total_profit = 0;

            if ($latest_report_date) {
                $date_filter = date('Y-m-d', strtotime($latest_report_date));

                $sql_usd = "SELECT SUM(bs_mapping_profit_orders_usd_bwd.total) AS totalordersusd
                        FROM `bs_mapping_profit_sumusd_bwd`
                        LEFT OUTER JOIN bs_mapping_profit_orders_usd_bwd ON bs_mapping_profit_orders_usd_bwd.mapping_id=bs_mapping_profit_sumusd_bwd.id
                        WHERE DATE(bs_mapping_profit_sumusd_bwd.mapped) = '" . $date_filter . "'";
                $rst_usd = $dbc->Query($sql_usd);
                $row_usd = $dbc->Fetch($rst_usd);
                $totalordersusd = (isset($row_usd['totalordersusd']) && $row_usd['totalordersusd'] != null && $row_usd['totalordersusd'] != 0 && $row_usd['totalordersusd'] != '') ? (float)$row_usd['totalordersusd'] : 0;

                $sql_thb = "SELECT SUM(bs_mapping_profit_orders_bwd.total) AS totalordersthb
                        FROM `bs_mapping_profit_bwd`
                        LEFT OUTER JOIN bs_mapping_profit_orders_bwd ON bs_mapping_profit_orders_bwd.mapping_id=bs_mapping_profit_bwd.id
                        WHERE DATE(bs_mapping_profit_bwd.mapped) = '" . $date_filter . "'";
                $rst_thb = $dbc->Query($sql_thb);
                $row_thb = $dbc->Fetch($rst_thb);
                $totalordersthb = (isset($row_thb['totalordersthb']) && $row_thb['totalordersthb'] != null && $row_thb['totalordersthb'] != 0 && $row_thb['totalordersthb'] != '') ? (float)$row_thb['totalordersthb'] : 0;

                $sql_usd_true = "SELECT SUM(amount * rate_finance) AS usd_true
                               FROM bs_purchase_usd_profit_bwd
                               WHERE (bs_purchase_usd_profit_bwd.value_date = '" . $date_filter . "')
                                 AND (bs_purchase_usd_profit_bwd.status <> -1)
                                 AND (bs_purchase_usd_profit_bwd.type LIKE 'physical' OR bs_purchase_usd_profit_bwd.type LIKE 'MTM')
                                 AND bs_purchase_usd_profit_bwd.comment = 'BWD'
                                 AND bs_purchase_usd_profit_bwd.date >= '2025-10-01'";
                $rst_usd_true = $dbc->Query($sql_usd_true);
                $row_usd_true = $dbc->Fetch($rst_usd_true);
                $usd_true = isset($row_usd_true['usd_true']) ? (float)$row_usd_true['usd_true'] : 0;

                $sql_thb_true = "SELECT SUM(THBValue) AS thb_true
                               FROM bs_purchase_spot_profit_bwd
                               WHERE (bs_purchase_spot_profit_bwd.value_date = '" . $date_filter . "')
                                 AND bs_purchase_spot_profit_bwd.parent IS NULL
                                 AND flag_hide = 0
                                 AND bs_purchase_spot_profit_bwd.rate_spot > 0
                                 AND (bs_purchase_spot_profit_bwd.type LIKE 'physical' OR bs_purchase_spot_profit_bwd.type LIKE 'MTM')
                                 AND (bs_purchase_spot_profit_bwd.status > 0 OR bs_purchase_spot_profit_bwd.status = -1)
                                 AND bs_purchase_spot_profit_bwd.date >= '2025-10-01' 
                                 AND bs_purchase_spot_profit_bwd.currency = 'THB'
                                 AND bs_purchase_spot_profit_bwd.supplier_id = '28'
                                 AND bs_purchase_spot_profit_bwd.ref LIKE '%แท่งดี%'";
                $rst_thb_true = $dbc->Query($sql_thb_true);
                $row_thb_true = $dbc->Fetch($rst_thb_true);
                $thb_true = isset($row_thb_true['thb_true']) ? (float)$row_thb_true['thb_true'] : 0;

                $latest_total_profit = ($totalordersusd - $usd_true) + ($totalordersthb - $thb_true);
            }

            $profit_class = $latest_total_profit < 0 ? 'negative' : 'positive';
            ?>

            <div class="balance-card ">
                <div class="balance-header">
                    <div class="balance-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="balance-value">
                        <h3 class="balance-amount <?php echo $profit_class; ?>">
                            <?php echo number_format($latest_total_profit, 2); ?>
                        </h3>
                        <div class="balance-rate balance-date">
                            <?php echo isset($latest_report_date) ? date('d/m/Y', strtotime($latest_report_date)) : 'ยังไม่มีข้อมูล'; ?>
                        </div>
                    </div>
                </div>
                <div class="balance-title">
                    <span class="title-icon profit">
                        <i class="fas fa-trophy"></i>
                    </span>
                    Balance Profit
                </div>
                <p class="balance-subtitle">
                    ยอดกำไรขาดทุนล่าสุดจากการซื้อขายทั้งหมด
                </p>
            </div>
        </div>
    </div>
</div>