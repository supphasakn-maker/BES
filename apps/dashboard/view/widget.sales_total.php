<?php
$total = $dbc->GetRecord("bs_orders", "SUM(net)", "status > 0 AND DATE(date) = '" . date("Y-m-d", $today) . "'");

// แปลง null เป็น 0
$total_amount = $total[0] ?? 0;

$data = array();
for ($t = $today - (86400 * 5); $t <= $today; $t += 86400) {
    $total_each = $dbc->GetRecord("bs_orders", "SUM(net)", "status > 0 AND DATE(date) = '" . date("Y-m-d", $t) . "'");
    // แปลง null เป็น 0 สำหรับแต่ละวัน
    $amount_each = $total_each[0] ?? 0;
    array_push($data, $amount_each);
}
?>

<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <h3>&#3647;&nbsp;</h3>
            <h3 class="card-title mb-0 mr-auto">
                <?php echo number_format($total_amount, 2); ?>
            </h3>
            <span id="total_sales"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-danger">ยอดขาย (ประจำวัน Bowins Silver)</h6>
        <p class="small text-secondary mb-0">
            ยอดขายประจำวันนี้ BWS <?php echo date("d-m-Y", $today); ?>
        </p>
    </div>
</div>
