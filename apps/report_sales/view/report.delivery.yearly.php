<?php

$year = $_POST['year'] ?? date('Y');
$aSum = array(0, 0, 0, 0, 0);
$aSumUSD = array(0, 0, 0, 0, 0, 0);


function getThaiMonth($month)
{
	$thaiMonths = array(
		1 => 'มกราคม',
		2 => 'กุมภาพันธ์',
		3 => 'มีนาคม',
		4 => 'เมษายน',
		5 => 'พฤษภาคม',
		6 => 'มิถุนายน',
		7 => 'กรกฎาคม',
		8 => 'สิงหาคม',
		9 => 'กันยายน',
		10 => 'ตุลาคม',
		11 => 'พฤศจิกายน',
		12 => 'ธันวาคม'
	);
	return $thaiMonths[$month] ?? $month;
}


$sql = "SELECT 
    COUNT(id) AS total_order,
    MONTH(delivery_date) AS month,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net
FROM bs_orders 
WHERE YEAR(delivery_date) = '$year'
AND bs_orders.status > 0
GROUP BY MONTH(delivery_date)
ORDER BY MONTH(delivery_date)";

$rst = $dbc->Query($sql);
$monthlyData = array();


while ($order = $dbc->Fetch($rst)) {
	$monthlyData[] = $order;
	$aSum[0] += $order['total_order'];
	$aSum[1] += $order['amount'];
	$aSum[2] += $order['total'];
	$aSum[3] += $order['vat'];
	$aSum[4] += $order['net'];
}


$sql_usd = "SELECT 
    COUNT(id) AS total_order,
    MONTH(delivery_date) AS month,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net,
    SUM(usd) AS usd_amount
FROM bs_orders 
WHERE YEAR(delivery_date) = '$year'
AND bs_orders.status > 0
AND bs_orders.usd > 0
GROUP BY MONTH(delivery_date)
ORDER BY MONTH(delivery_date)";

$rst_usd = $dbc->Query($sql_usd);
$monthlyDataUSD = array();


while ($order = $dbc->Fetch($rst_usd)) {
	$monthlyDataUSD[] = $order;
	$aSumUSD[0] += $order['total_order'];
	$aSumUSD[1] += $order['amount'];
	$aSumUSD[2] += $order['total'];
	$aSumUSD[3] += $order['vat'];
	$aSumUSD[4] += $order['net'];
	$aSumUSD[5] += $order['usd_amount'];
}
?>

<style>
	.table th,
	.table td {
		padding: 8px !important;
	}

	.text-right {
		text-align: right;
	}

	.text-center {
		text-align: center;
	}

	.pr-2 {
		padding-right: 8px;
	}

	.fw-bold {
		font-weight: bold;
	}

	.card {
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}

	.progress {
		height: 20px;
	}
</style>


<div class="container-fluid">

	<!-- ตารางยอดขายรายเดือน THB -->
	<div class="card mb-4">
		<div class="card-header bg-dark text-white">
			<h5 class="mb-0">ยอดขายรายเดือน THB</h5>
		</div>
		<div class="card-body p-0">
			<table class="table table-sm table-bordered table-striped mb-0">
				<thead class="bg-secondary text-white">
					<tr>
						<th class="text-center">เดือน</th>
						<th class="text-center">จำนวนการสั่งซื้อ</th>
						<th class="text-center">จำนวนกิโลกรัม</th>
						<th class="text-center">ยอดรวม</th>
						<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
						<th class="text-center">ยอดรวมสุทธิ</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($monthlyData as $order): ?>
						<tr>
							<td class="text-center">
								<strong><?= getThaiMonth($order['month']) ?></strong>
								<br><small class="text-muted"><?= $order['month'] ?>/<?= $year ?></small>
							</td>
							<td class="text-center"><?= number_format($order['total_order']) ?></td>
							<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
							<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
							<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
							<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-dark text-white">
					<tr>
						<th class="text-center" colspan="2">รวมทั้งหมด <?= number_format($aSum[0]) ?> รายการ</th>
						<th class="text-right pr-2"><?= number_format($aSum[1], 4) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[2], 2) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[3], 2) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[4], 2) ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<!-- ตารางยอดขายรายเดือน USD -->
	<?php if (!empty($monthlyDataUSD)): ?>
		<div class="card mb-4">
			<div class="card-header bg-dark text-white">
				<h5 class="mb-0">ยอดขายรายเดือน USD</h5>
			</div>
			<div class="card-body p-0">
				<table class="table table-sm table-bordered table-striped mb-0">
					<thead class="bg-dark text-white">
						<tr>
							<th class="text-center">เดือน</th>
							<th class="text-center">จำนวนการสั่งซื้อ</th>
							<th class="text-center">จำนวนกิโลกรัม</th>
							<th class="text-center">ยอดรวม (THB)</th>
							<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
							<th class="text-center">ยอดรวมสุทธิ (THB)</th>
							<th class="text-center">ยอดรวม (USD)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($monthlyDataUSD as $order): ?>
							<tr>
								<td class="text-center">
									<strong><?= getThaiMonth($order['month']) ?></strong>
									<br><small class="text-muted"><?= $order['month'] ?>/<?= $year ?></small>
								</td>
								<td class="text-center"><?= number_format($order['total_order']) ?></td>
								<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
								<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
								<td class="text-right pr-2 text-dark"><strong>$<?= number_format($order['usd_amount'], 2) ?></strong></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot class="bg-dark text-white">
						<tr>
							<th class="text-center" colspan="2">รวม USD <?= number_format($aSumUSD[0]) ?> รายการ</th>
							<th class="text-right pr-2"><?= number_format($aSumUSD[1], 4) ?></th>
							<th class="text-right pr-2"><?= number_format($aSumUSD[2], 2) ?></th>
							<th class="text-right pr-2"><?= number_format($aSumUSD[3], 2) ?></th>
							<th class="text-right pr-2"><?= number_format($aSumUSD[4], 2) ?></th>
							<th class="text-right pr-2"><strong>$<?= number_format($aSumUSD[5], 2) ?></strong></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	<?php endif; ?>

	<!-- สรุปเปรียบเทียบ -->
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h6 class="mb-0">สรุปยอดขายรวม</h6>
				</div>
				<div class="card-body">
					<table class="table table-sm mb-0">
						<tr>
							<td><strong>จำนวนรายการทั้งหมด:</strong></td>
							<td class="text-right"><?= number_format($aSum[0]) ?> รายการ</td>
						</tr>
						<tr>
							<td><strong>จำนวนเดือนที่มีการส่ง:</strong></td>
							<td class="text-right"><?= count($monthlyData) ?> เดือน</td>
						</tr>
						<tr>
							<td><strong>น้ำหนักรวม:</strong></td>
							<td class="text-right"><?= number_format($aSum[1], 4) ?> กก.</td>
						</tr>
						<tr>
							<td><strong>ยอดรวมสุทธิ:</strong></td>
							<td class="text-right"><?= number_format($aSum[4], 2) ?> บาท</td>
						</tr>
						<tr class="table-info">
							<td><strong>ยอดรวม THB:</strong></td>
							<td class="text-right fw-bold"><?= number_format(($aSum[4] - $aSumUSD[4]), 2) ?> บาท</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h6 class="mb-0">สรุปยอดขาย USD</h6>
				</div>
				<div class="card-body">
					<table class="table table-sm mb-0">
						<tr>
							<td><strong>จำนวนรายการ USD:</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[0]) ?> รายการ</td>
						</tr>
						<tr>
							<td><strong>จำนวนเดือนที่มี USD:</strong></td>
							<td class="text-right"><?= count($monthlyDataUSD) ?> เดือน</td>
						</tr>
						<tr>
							<td><strong>น้ำหนัก USD:</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[1], 4) ?> กก.</td>
						</tr>
						<tr>
							<td><strong>ยอดรวมสุทธิ (THB):</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[4], 2) ?> บาท</td>
						</tr>
						<tr class="table-dark">
							<td><strong>ยอดรวม USD:</strong></td>
							<td class="text-right text-dark fw-bold">$<?= number_format($aSumUSD[5], 2) ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h6 class="mb-0">เปรียบเทียบสัดส่วน</h6>
				</div>
				<div class="card-body">
					<!-- สัดส่วนรายการ -->
					<div class="mb-3">
						<small class="text-muted">สัดส่วนรายการ</small>
						<?php $order_percent = $aSum[0] > 0 ? ($aSumUSD[0] / $aSum[0]) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-dark" style="width: <?= $order_percent ?>%">
								<?= number_format($order_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= number_format($aSumUSD[0]) ?> | THB: <?= number_format($aSum[0] - $aSumUSD[0]) ?> รายการ</small>
					</div>

					<!-- สัดส่วนมูลค่า -->
					<div class="mb-3">
						<small class="text-muted">สัดส่วนมูลค่า (บาท)</small>
						<?php $value_percent = $aSum[4] > 0 ? ($aSumUSD[4] / $aSum[4]) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-dark" style="width: <?= $value_percent ?>%">
								<?= number_format($value_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= number_format($aSumUSD[4], 0) ?> | THB: <?= number_format(($aSum[4] - $aSumUSD[4]), 0) ?> บาท</small>
					</div>

					<!-- สัดส่วนน้ำหนัก -->
					<div class="mb-3">
						<small class="text-muted">สัดส่วนน้ำหนัก</small>
						<?php $weight_percent = $aSum[1] > 0 ? ($aSumUSD[1] / $aSum[1]) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-dark" style="width: <?= $weight_percent ?>%">
								<?= number_format($weight_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= number_format($aSumUSD[1], 2) ?> | THB: <?= number_format(($aSum[1] - $aSumUSD[1]), 2) ?> กก.</small>
					</div>

					<!-- สัดส่วนเดือนที่มีการส่ง -->
					<div>
						<small class="text-muted">สัดส่วนเดือนที่มี USD</small>
						<?php $month_percent = count($monthlyData) > 0 ? (count($monthlyDataUSD) / count($monthlyData)) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-dark" style="width: <?= $month_percent ?>%">
								<?= number_format($month_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= count($monthlyDataUSD) ?> | THB: <?= (count($monthlyData) - count($monthlyDataUSD)) ?> เดือน</small>
					</div>
				</div>
			</div>
		</div>
	</div>



</div>