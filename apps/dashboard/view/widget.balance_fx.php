<?php
$balance = -43351.56;

// คำนวณผลกระทบจาก bs_purchase_spot
$sql_spot = "SELECT amount, rate_spot, rate_pmdc
             FROM bs_purchase_spot
             WHERE DATE(date) > '2023-11-16'
               AND currency != 'THB'
               AND type LIKE 'physical'
               AND rate_spot > 0
               AND status > 0
               AND confirm IS NOT NULL
               AND NOT EXISTS (SELECT 1 FROM bs_purchase_spot bps2 WHERE bps2.parent = bs_purchase_spot.id)";
$rst_spot = $dbc->Query($sql_spot);
while ($item_spot = $dbc->Fetch($rst_spot)) {
    $total_spot = $item_spot['amount'] * ($item_spot['rate_spot'] + $item_spot['rate_pmdc']) * 32.1507;
    $balance -= $total_spot;
}

// คำนวณผลกระทบจาก bs_purchase_usd
$sql_usd = "SELECT amount, rate_finance
            FROM bs_purchase_usd
            WHERE DATE(date) > '2023-11-16'
              AND parent IS NULL
              AND type LIKE 'physical'
              AND confirm IS NOT NULL";
$rst_usd = $dbc->Query($sql_usd);
$total_usd_amount = 0;
while ($item_usd = $dbc->Fetch($rst_usd)) {
    $balance += $item_usd['amount'];
    $total_usd_amount = $item_usd['rate_finance']; // เก็บค่า rate_finance ล่าสุดไว้แสดง (ถ้าต้องการ)
}

?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="trending-up" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($balance, 4); ?></h3>
            <span id="yourPhotos"><?php echo number_format($total_usd_amount, 4); ?></span>
        </div>
        <h6 class="text-danger">Balance FX</h6>
        <p class="small text-secondary mb-0">Balance FX ล่าสุด</p>
    </div>
</div>