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
        <!-- Balance Silver Card -->
        <div class="col-sm-6 col-xl-4 mb-3">
            <?php
            $sql = "WITH AllDates AS (
                SELECT DISTINCT DATE(date) AS dt
                FROM bs_orders_profit
                WHERE DATE(date) > '2023-09-29' AND parent IS NULL AND status > -1
                UNION
                SELECT DISTINCT DATE(date) AS dt
                FROM bs_purchase_spot
                WHERE DATE(date) > '2023-09-29'),
                OrderMarginChanges AS (
                    SELECT DATE(o.date) AS dt, COALESCE(SUM(o.amount), 0) AS order_margin_change
                    FROM bs_orders_profit o
                    WHERE o.parent IS NULL AND o.status > -1 AND DATE(o.date) > '2023-09-29'
                    GROUP BY DATE(o.date)
                ),
                PurchaseMarginChanges AS (
                    SELECT DATE(ps.date) AS dt, COALESCE(SUM(ps.amount), 0) AS purchase_margin_change
                    FROM bs_purchase_spot ps
                    WHERE (ps.type LIKE 'physical' OR ps.type LIKE 'stock')
                    AND ps.rate_spot > 0 AND ps.status > -1 AND ps.confirm IS NOT NULL
                    AND NOT EXISTS (SELECT 1 FROM bs_purchase_spot ps2 WHERE ps2.parent = ps.id)
                    AND DATE(ps.date) > '2023-09-29'
                    GROUP BY DATE(ps.date)
                ),
                DailyChanges AS (
                    SELECT
                        ad.dt,
                        COALESCE(pm.purchase_margin_change, 0) - COALESCE(om.order_margin_change, 0) AS daily_change
                    FROM AllDates ad
                    LEFT JOIN PurchaseMarginChanges pm ON ad.dt = pm.dt
                    LEFT JOIN OrderMarginChanges om ON ad.dt = om.dt
                )
                SELECT
                    (SELECT -739) + COALESCE(SUM(daily_change), 0) AS latest_balance,
                    (SELECT bps_inner.rate_spot
                    FROM bs_purchase_spot bps_inner
                    WHERE (bps_inner.type LIKE 'physical' OR bps_inner.type LIKE 'stock')
                    AND bps_inner.rate_spot > 0
                    AND bps_inner.status > -1
                    AND bps_inner.confirm IS NOT NULL
                    AND NOT EXISTS (SELECT 1 FROM bs_purchase_spot bps3 WHERE bps3.parent = bps_inner.id)
                    ORDER BY bps_inner.date DESC, bps_inner.id DESC
                    LIMIT 1) AS latest_usd_rate
                FROM DailyChanges;";

            $rst = $dbc->Query($sql);
            $data = $dbc->Fetch($rst);
            $latest_balance = $data['latest_balance'];
            $latest_usd = $data['latest_usd_rate'];
            $balance_class = $latest_balance < 0 ? 'negative' : 'positive';
            ?>

            <div class="balance-card">
                <div class="balance-header">
                    <div class="balance-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="balance-value">
                        <h3 class="balance-amount <?php echo $balance_class; ?>">
                            <?php echo number_format($latest_balance, 4); ?>
                        </h3>
                        <div class="balance-rate">
                            USD Rate: <?php echo number_format($latest_usd, 4); ?>
                        </div>
                    </div>
                </div>
                <div class="balance-title">
                    <span class="title-icon silver">
                        <i class="fas fa-gem"></i>
                    </span>
                    Balance Silver
                </div>
                <p class="balance-subtitle">
                    ยอดคงเหลือเงินตราประเภทเงิน (Silver) ล่าสุด
                </p>
            </div>
        </div>

        <!-- Balance FX Card -->
        <div class="col-sm-6 col-xl-4 mb-3">
            <?php
            $balance = 207973.6700;
            $total_margin = 0;

            // คำนวณผลรวม amount จาก bs_purchase_usd (ทำให้ margin ลดลง)
            $sql_usd = "SELECT SUM(amount) AS total_usd_amount
                        FROM bs_purchase_usd
                        WHERE YEAR(date) > 2024
                          AND date >= '2025-05-02'
                          AND type LIKE 'Physical'
                          AND parent IS NULL
                          AND confirm IS NOT NULL";
            $rst_usd = $dbc->Query($sql_usd);
            $data_usd = $dbc->Fetch($rst_usd);
            $total_usd_amount = $data_usd['total_usd_amount'] ?: 0;
            $total_margin -= $total_usd_amount;

            // คำนวณผลรวม amount จาก bs_purchase_usd_profit (ทำให้ margin เพิ่มขึ้น)
            $sql_profit_amount = "SELECT SUM(amount) AS total_profit_amount
                                  FROM bs_purchase_usd_profit
                                  WHERE YEAR(value_date) > 2024
                                    AND value_date >= '2025-05-02'
                                    AND (type LIKE 'Physical' OR type LIKE 'MTM')
                                    AND parent IS NULL
                                    AND confirm IS NOT NULL";
            $rst_profit_amount = $dbc->Query($sql_profit_amount);
            $data_profit_amount = $dbc->Fetch($rst_profit_amount);
            $total_profit_amount = $data_profit_amount['total_profit_amount'] ?: 0;
            $total_margin += $total_profit_amount;

            // ดึง rate_finance ล่าสุด
            $sql_latest_rate = "SELECT rate_finance
                                FROM bs_purchase_usd_profit
                                WHERE YEAR(value_date) > 2024
                                ORDER BY value_date DESC, id DESC
                                LIMIT 1";
            $rst_latest_rate = $dbc->Query($sql_latest_rate);
            $data_latest_rate = $dbc->Fetch($rst_latest_rate);
            $latest_rate_finance = $data_latest_rate['rate_finance'] ?: 0;

            // ปรับ balance ครั้งเดียว
            $balance -= $total_margin;
            $fx_balance_class = $balance < 0 ? 'negative' : 'positive';
            ?>

            <div class="balance-card">
                <div class="balance-header">
                    <div class="balance-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="balance-value">
                        <h3 class="balance-amount <?php echo $fx_balance_class; ?>">
                            <?php echo number_format($balance, 4); ?>
                        </h3>
                        <div class="balance-rate">
                            Rate: <?php echo number_format($latest_rate_finance, 4); ?>
                        </div>
                    </div>
                </div>
                <div class="balance-title">
                    <span class="title-icon fx">
                        <i class="fas fa-exchange-alt"></i>
                    </span>
                    Balance FX
                </div>
                <p class="balance-subtitle">
                    ยอดคงเหลือ (Fifo USD) ล่าสุด
                </p>
            </div>
        </div>

        <!-- Balance Profit Card -->
        <div class="col-sm-6 col-xl-4 mb-3">
            <?php
            $sql_latest_date = "(SELECT MAX(mapped) AS latest_date FROM bs_mapping_profit_sumusd)
                            UNION
                            (SELECT MAX(mapped) AS latest_date FROM bs_mapping_profit)
                            UNION
                            (SELECT MAX(value_date) AS latest_date FROM bs_purchase_usd_profit)
                            UNION
                            (SELECT MAX(value_date) AS latest_date FROM bs_purchase_spot_profit)
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

                $sql_usd = "SELECT SUM(bs_mapping_profit_orders_usd.total) AS totalordersusd
                        FROM `bs_mapping_profit_sumusd`
                        LEFT OUTER JOIN bs_mapping_profit_orders_usd ON bs_mapping_profit_orders_usd.mapping_id=bs_mapping_profit_sumusd.id
                        WHERE DATE(bs_mapping_profit_sumusd.mapped) = '" . $date_filter . "'";
                $rst_usd = $dbc->Query($sql_usd);
                $row_usd = $dbc->Fetch($rst_usd);
                $totalordersusd = (isset($row_usd['totalordersusd']) && $row_usd['totalordersusd'] != null && $row_usd['totalordersusd'] != 0 && $row_usd['totalordersusd'] != '') ? (float)$row_usd['totalordersusd'] : 0;

                $sql_thb = "SELECT SUM(bs_mapping_profit_orders.total) AS totalordersthb
                        FROM `bs_mapping_profit`
                        LEFT OUTER JOIN bs_mapping_profit_orders ON bs_mapping_profit_orders.mapping_id=bs_mapping_profit.id
                        WHERE DATE(bs_mapping_profit.mapped) = '" . $date_filter . "'";
                $rst_thb = $dbc->Query($sql_thb);
                $row_thb = $dbc->Fetch($rst_thb);
                $totalordersthb = (isset($row_thb['totalordersthb']) && $row_thb['totalordersthb'] != null && $row_thb['totalordersthb'] != 0 && $row_thb['totalordersthb'] != '') ? (float)$row_thb['totalordersthb'] : 0;

                $sql_usd_true = "SELECT SUM(amount * rate_finance) AS usd_true
                               FROM bs_purchase_usd_profit
                               WHERE (bs_purchase_usd_profit.value_date = '" . $date_filter . "')
                                 AND ( bs_purchase_usd_profit.status <> -1)
                                 AND (bs_purchase_usd_profit.type LIKE 'physical' OR bs_purchase_usd_profit.type LIKE 'MTM')
                                 AND YEAR(date) > 2024";
                $rst_usd_true = $dbc->Query($sql_usd_true);
                $row_usd_true = $dbc->Fetch($rst_usd_true);
                $usd_true = isset($row_usd_true['usd_true']) ? (float)$row_usd_true['usd_true'] : 0;

                $sql_thb_true = "SELECT SUM(THBValue) AS thb_true
                               FROM bs_purchase_spot_profit
                               WHERE (bs_purchase_spot_profit.value_date = '" . $date_filter . "')
                                 AND bs_purchase_spot_profit.parent IS NULL
                                 AND flag_hide = 0
                                 AND bs_purchase_spot_profit.rate_spot > 0
                                 AND (bs_purchase_spot_profit.type LIKE 'physical' OR bs_purchase_spot_profit.type LIKE 'MTM')
                                 AND (bs_purchase_spot_profit.status > 0 OR bs_purchase_spot_profit.status = -1)
                                 AND YEAR(date) > 2024 AND bs_purchase_spot_profit.currency = 'THB'";
                $rst_thb_true = $dbc->Query($sql_thb_true);
                $row_thb_true = $dbc->Fetch($rst_thb_true);
                $thb_true = isset($row_thb_true['thb_true']) ? (float)$row_thb_true['thb_true'] : 0;

                $latest_total_profit = ($totalordersusd - $usd_true) + ($totalordersthb - $thb_true);
            }

            $profit_class = $latest_total_profit < 0 ? 'negative' : 'positive';
            ?>

            <div class="balance-card">
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