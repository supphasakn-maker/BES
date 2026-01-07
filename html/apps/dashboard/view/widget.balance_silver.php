 <?php
	$sql = "WITH AllDates AS (
            SELECT DISTINCT DATE(date) AS dt
            FROM bs_orders
            WHERE DATE(date) > '2023-09-29' AND parent IS NULL AND status > -1
            UNION
            SELECT DISTINCT DATE(date) AS dt
            FROM bs_purchase_spot
            WHERE DATE(date) > '2023-09-29'),
            OrderMarginChanges AS (
                SELECT DATE(o.date) AS dt, COALESCE(SUM(o.amount), 0) AS order_margin_change
                FROM bs_orders o
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
	?>
 <div class="card h-100">
 	<div class="card-body">
 		<div class="flex-center justify-content-start mb-2">
 			<i data-feather="trending-up" class="mr-2 font-size-lgs"></i>
 			<h3 class="card-title mb-0 mr-auto"><?php echo number_format($latest_balance, 4); ?></h3>
 			<span id="yourPhotos" style="color: blue;">USD: <?php echo number_format($latest_usd, 4); ?> </span>
 		</div>
 		<h6 class="text-success">Balance Silver</h6>
 		<p class="small text-secondary mb-0">Balance Silver ล่าสุด</p>
 	</div>
 </div>