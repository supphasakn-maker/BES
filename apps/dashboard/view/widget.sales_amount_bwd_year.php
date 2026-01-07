<?php
$total = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "status > 0 AND DATE_FORMAT(created,'%Y') = '" . date("Y", $today) . "'");
// แปลง null เป็น 0
$total_amount = $total[0] ?? 0;

$data = array();

for ($month = 1; $month <= 12; $month++) {
    $first_day_of_month = strtotime(date("Y", $today) . "-" . sprintf("%02d", $month) . "-01");

    $total_each_month = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "status > 0 AND DATE_FORMAT(created,'%Y-%m') = '" . date("Y-m", $first_day_of_month) . "'");
    array_push($data, $total_each_month[0]);
}
?>
<div class="card h-100">
    <div class="card-body">
        <div class="flex-center justify-content-start mb-2">
            <i data-feather="book" class="mr-2 font-size-lgs"></i>
            <h3 class="card-title mb-0 mr-auto"><?php echo number_format($total_amount, 4); ?></h3>
            <span id="amount_year_bwd"><?php echo join(",", $data); ?></span>
        </div>
        <h6 class="text-primary">ยอดขาย (แท่ง Bowins Design)</h6>
        <p class="small text-secondary mb-0">ยอดขายประจำปี BWD <?php echo date("Y", $today); ?></p>
    </div>
</div>
