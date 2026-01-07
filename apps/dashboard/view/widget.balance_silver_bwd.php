<?php
$sql = "WITH AllDates AS (
            SELECT DISTINCT DATE(created) AS dt
            FROM bs_orders_bwd
            WHERE DATE(created) >= '2025-10-01' 
            AND parent IS NULL 
            AND status > -1
            AND product_id IN (1, 2, 3)
            UNION
            SELECT DISTINCT DATE(date) AS dt
            FROM bs_purchase_spot
            WHERE DATE(date) >= '2025-10-01' 
            AND ((currency = 'USD' AND ref = 'BWD') OR (currency = 'THB' AND supplier_id = '28'))),
            OrderMarginChanges AS (
                SELECT 
                    DATE(parent.created) AS dt, 
                    COALESCE(SUM(
                        CASE 
                            WHEN o.product_id = 1 THEN o.amount * 0.015
                            WHEN o.product_id = 2 THEN o.amount * 0.050
                            WHEN o.product_id = 3 THEN o.amount * 0.150
                            ELSE 0
                        END
                    ), 0) AS order_margin_change
                FROM bs_orders_bwd parent
                LEFT JOIN bs_orders_bwd o ON (o.id = parent.id OR o.parent = parent.id)
                WHERE parent.parent IS NULL 
                AND o.status > 0
                AND o.product_id IN (1, 2, 3)
                AND DATE(parent.created) >= '2025-10-01'
                GROUP BY DATE(parent.created)
            ),
            PurchaseMarginChanges AS (
                SELECT 
                    DATE(ps.date) AS dt, 
                    COALESCE(SUM(ps.amount), 0) AS purchase_margin_change
                FROM bs_purchase_spot ps
                WHERE (ps.type LIKE 'physical' OR ps.type LIKE 'stock')
                AND ps.rate_spot > 0 
                AND ps.status > -1 
                AND ps.confirm IS NOT NULL
                AND NOT EXISTS (SELECT 1 FROM bs_purchase_spot ps2 WHERE ps2.parent = ps.id)
                AND ((ps.currency = 'USD' AND ps.ref = 'BWD') OR (ps.currency = 'THB' AND ps.supplier_id = '28'))
                AND DATE(ps.date) >= '2025-10-01'
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
                COALESCE(SUM(daily_change), 0) AS latest_balance,
                (SELECT bps_inner.rate_spot
                FROM bs_purchase_spot bps_inner
                WHERE (bps_inner.type LIKE 'physical' OR bps_inner.type LIKE 'stock')
                AND bps_inner.rate_spot > 0
                AND bps_inner.status > -1
                AND bps_inner.confirm IS NOT NULL
                AND NOT EXISTS (SELECT 1 FROM bs_purchase_spot bps3 WHERE bps3.parent = bps_inner.id)
                AND ((bps_inner.currency = 'USD' AND bps_inner.ref = 'BWD') OR (bps_inner.currency = 'THB' AND bps_inner.supplier_id = '28'))
                ORDER BY bps_inner.date DESC, bps_inner.id DESC
                LIMIT 1) AS latest_usd_rate
            FROM DailyChanges;";

$rst = $dbc->Query($sql);
$data = $dbc->Fetch($rst);
$latest_balance = $data['latest_balance'];
$latest_usd = $data['latest_usd_rate'];
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="trending-up" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($latest_balance, 4); ?></h3>
            <span id="yourPhotos" style="color: blue;">USD: <?php echo number_format($latest_usd, 4); ?> </span>
        </div>
        <h6 class="text-success">Balance Silver BWD</h6>
        <p class="small text-secondary mb-0">Balance Silver BWD ล่าสุด</p>
    </div>
</div>