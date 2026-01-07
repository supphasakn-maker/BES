<?php
$total = $dbc->GetRecord("bs_orders_bwd", "SUM(net)", "status > 0 AND DATE(created) = '" . date("Y-m-d", $today) . "'");
$data = array();


for ($t = $today - (86400 * 5); $t <= $today; $t += 86400) {
    $total_each = $dbc->GetRecord("bs_orders_bwd", "SUM(net)", "status > 0 AND DATE(created) = '" . date("Y-m-d", $t) . "'");
    array_push($data, $total_each[0]);
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <h3>&#3647;&nbsp;</h3>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total[0], 2); ?></h3>
            <span id="total_sales_bwd"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-danger">ยอดขาย (ประจำวัน Bowins Sesign)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำวันนี้ BWD <?php echo date("d-m-Y", $today); ?></p>
    </div>
</div>