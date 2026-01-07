<?php
// สมมติว่า $today มีค่า timestamp อยู่แล้ว
$date_today = date("Y-m-d", $today);

// Query แบบรวม order ย่อย ตามที่ให้มา
$sql = "
    SELECT 
        SUM(o.amount) AS amount
    FROM bs_orders_bwd parent
    LEFT JOIN bs_orders_bwd o ON (o.id = parent.id OR o.parent = parent.id)
    WHERE DATE(parent.date) = '$date_today'
        AND parent.parent IS NULL
        AND o.status > 0
";

$query = $dbc->Query($sql);
$total = $dbc->Fetch($query); // $total['amount'], $total['net'], ...

?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total['amount'], 4); ?></h3>
            <span id="bwd"><?php echo number_format($total['net'], 2); ?> บาท</span>
        </div>
        <h6 class="text-primary">ยอดขาย (แท่ง Bowins Design)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำวันนี้ BWD</p>
    </div>
</div>