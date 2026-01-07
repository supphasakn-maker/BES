<?php
$year_now = date("Y", $today);

// น้ำหนัก mapping เฉพาะสินค้า 1,2,3
$selectKg = "
    COALESCE(
        SUM(
            CASE 
                WHEN product_id = 1 THEN amount * 0.015
                WHEN product_id = 2 THEN amount * 0.050
                WHEN product_id = 3 THEN amount * 0.150
                ELSE 0
            END
        ), 0
    )
";

// ✅ ยอดรวมทั้งปี (กิโลกรัม)
$total = $dbc->GetRecord(
    "bs_orders_bwd",
    $selectKg,
    "status > 0 
     AND product_id IN (1,2,3)
     AND DATE_FORMAT(`created`,'%Y') = '" . $dbc->Escape_String($year_now) . "'"
);

// ✅ ยอดรายเดือน (มกราคม – ธันวาคม)
$data = array();
for ($month = 1; $month <= 12; $month++) {
    $ym = $year_now . "-" . sprintf("%02d", $month);
    $row = $dbc->GetRecord(
        "bs_orders_bwd",
        $selectKg,
        "status > 0 
         AND product_id IN (1,2,3)
         AND DATE_FORMAT(`created`,'%Y-%m') = '" . $dbc->Escape_String($ym) . "'"
    );
    $val = isset($row[0]) ? (float)$row[0] : (float)array_values($row)[0];
    $data[] = $val;
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto">
                <?php echo number_format(isset($total[0]) ? $total[0] : array_values($total)[0], 4); ?>
            </h3>
            <span id="amount_year_bwd_kg"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขาย Bowins Design (กิโลกรัม)</h6>
        <p class="small text-secondary mb-0">
            ยอดขายประจำปี BWD <?php echo $year_now; ?>
        </p>
    </div>
</div>