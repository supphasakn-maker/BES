<?php

$total = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND DATE_FORMAT(date,'%Y') = '" . date("Y", $today) . "'");

$data = array();

for ($month = 1; $month <= 12; $month++) {
    $first_day_of_month = strtotime(date("Y", $today) . "-" . sprintf("%02d", $month) . "-01");
    
    $total_each_month = $dbc->GetRecord("bs_orders", "SUM(amount)", "status > 0 AND DATE_FORMAT(date,'%Y-%m') = '" . date("Y-m", $first_day_of_month) . "'");
    array_push($data, $total_each_month[0]);
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total[0], 4); ?></h3>
            <span id="amount_year"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขาย (กิโลกรัม Bowins Silver)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำปี BWS <?php echo date("Y", $today); ?></p>
    </div>
</div>