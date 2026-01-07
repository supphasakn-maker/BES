<?php
$date_today = date("Y-m-d", $today);

$sql = "
    SELECT 
        COALESCE(SUM(
            CASE 
                WHEN o.product_id = 1 THEN o.amount * 0.015
                WHEN o.product_id = 2 THEN o.amount * 0.050
                WHEN o.product_id = 3 THEN o.amount * 0.150
                ELSE 0
            END
        ),0) AS kg_total
    FROM bs_orders_bwd parent
    LEFT JOIN bs_orders_bwd o 
        ON (o.id = parent.id OR o.parent = parent.id)
    WHERE DATE(parent.created) = '$date_today'
        AND parent.parent IS NULL
        AND o.status > 0
        AND o.product_id IN (1,2,3)  -- กันเหนียว เฉพาะสินค้าที่ไม่ใช่กล่อง
";

$query = $dbc->Query($sql);
$total = $dbc->Fetch($query); // $total['kg_total']

?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto">
                <?php echo number_format($total['kg_total'], 4); ?>
            </h3>
            <span id="bwd_kg">
                <?php echo number_format($total['kg_total'], 4); ?> กิโล
            </span>
        </div>
        <h6 class="text-primary">ยอดขาย Bowins Design(กิโลกรัม)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำวันนี้ BWD</p>
    </div>
</div>