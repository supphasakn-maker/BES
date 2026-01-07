<?php
$total = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND product_id = 1 AND DATE(date) = '" . date("Y-m-d", $today) . "'");

// แปลง null เป็น 0
$total_amount = $total[0] ?? 0;

$data = array();
for ($t = $today - (86400 * 5); $t <= $today; $t += 86400) {
    $total_each = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND product_id = 1 AND DATE(date) = '" . date("Y-m-d", $t) . "'");
    // แปลง null เป็น 0 สำหรับแต่ละวัน
    $amount_each = $total_each[0] ?? 0;
    array_push($data, $amount_each);
}
?>

<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto">
                <?php echo number_format($total_amount, 4); ?>
            </h3>
            <span id="sales_silver"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขายเม็ดเงิน (กิโลกรัม)</h6>
        <p class="small text-secondary mb-0">ยอดขายเม็ดเงินประจำวันนี้ BWS</p>
    </div>
</div>
