<?php
$total = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND DATE_FORMAT(date,'%Y-%m') = '" . date("Y-m", $today) . "'");
$data = array();


for ($t = $today - (86400 * 5); $t <= $today; $t += 86400) {
    $total_each = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND DATE_FORMAT(date,'%Y-%m') = '" . date("Y-m", $t) . "'");
    array_push($data, $total_each[0]);
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total[0], 4); ?></h3>
            <span id="amount_month"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขาย (กิโลกรัม Bowins Silver)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำเดือน BWS <?php echo date("m", $today); ?></p>
    </div>
</div>