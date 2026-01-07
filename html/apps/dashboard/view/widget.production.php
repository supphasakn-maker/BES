<?php
$total = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "DATE(submited) = '" . date("Y-m-d", $today) . "'");
$data = array();


for ($t = $today - (86400 * 5); $t <= $today; $t += 86400) {
	$total_each = $dbc->GetRecord("bs_productions", "SUM(weight_out_packing)", "DATE(submited) = '" . date("Y-m-d", $t) . "'");
	array_push($data, $total_each[0]);
}
?>
<div class="card h-100">
	<div class="card-body">
		<div class="flex-center justify-content-start mb-2">
			<i data-feather="file-plus" class="mr-2 font-size-lgs"></i>
			<h3 class="card-title mb-0 mr-auto"><?php echo number_format($total[0], 4); ?></h3>
			<span id="yourPhotos"><?php echo join(",", $data); ?></span>
		</div>
		<h6 class="text-success">ยอดผลิต</h6>
		<p class="small text-secondary mb-0">ยอดสั่งผลิตวันนี้</p>
	</div>
</div>