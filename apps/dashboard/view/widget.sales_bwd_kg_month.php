<?php
// สมมติว่ามี $today เป็น timestamp อยู่แล้ว
$ym_this_month = date("Y-m", $today);

// SELECT กิโลรวม (กก.) เฉพาะ product_id 1,2,3
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

// ยอดรวมของ "เดือนปัจจุบัน"
$total = $dbc->GetRecord(
    "bs_orders_bwd",
    $selectKg,
    "status > 0 
     AND product_id IN (1,2,3)
     AND DATE_FORMAT(`created`,'%Y-%m') = '" . $dbc->Escape_String($ym_this_month) . "'"
);

// สร้างสปาร์คไลน์ย้อนหลัง 6 เดือน (เก่าสุด → ใหม่สุด)
$data = array();
for ($i = 5; $i >= 0; $i--) {
    $ym = date("Y-m", strtotime("-{$i} months", $today));
    $row = $dbc->GetRecord(
        "bs_orders_bwd",
        $selectKg,
        "status > 0 
         AND product_id IN (1,2,3)
         AND DATE_FORMAT(`created`,'%Y-%m') = '" . $dbc->Escape_String($ym) . "'"
    );

    // รองรับทั้ง index 0 หรือชื่อคอลัมน์
    $val = isset($row[0]) ? (float)$row[0] : (float)array_values($row)[0];
    $data[] = $val; // เก็บเป็นตัวเลขดิบ
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto">
                <?php echo number_format(isset($total[0]) ? $total[0] : array_values($total)[0], 4); ?>
            </h3>
            <span id="amount_month_kg_bwd"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขาย Bowins Design (กิโลกรัม)</h6>
        <p class="small text-secondary mb-0">
            ยอดขายประจำเดือน BWD <?php echo date("m", $today); ?>
        </p>
    </div>
</div>