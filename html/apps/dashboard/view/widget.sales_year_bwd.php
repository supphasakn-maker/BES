<?php
$current_year = date("Y", $today);

$total = $dbc->GetRecord("bs_orders_bwd", "SUM(net)", "status > 0 AND DATE_FORMAT(created,'%Y') = '" . $current_year . "'");

$data = array();
for ($month = 1; $month <= 12; $month++) {

    $first_day_of_month = strtotime($current_year . "-" . sprintf("%02d", $month) . "-01");

    $total_each_month = $dbc->GetRecord("bs_orders_bwd", "SUM(net)", "status > 0 AND DATE_FORMAT(created,'%Y-%m') = '" . date("Y-m", $first_day_of_month) . "'");
    array_push($data, $total_each_month[0]);
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <h3>&#3647;&nbsp;</h3>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total[0], 2); ?></h3>
            <span id="sale_total_bwd_year"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-info">ยอดขายประจำปี Bowins Design</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำปี <?php echo $current_year; ?></p>
    </div>
</div>