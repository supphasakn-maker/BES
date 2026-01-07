<?php
// เตรียมข้อมูล
$month = $_POST['month'] ?? date('Y-m');
$aSum = array(0, 0, 0, 0, 0);
$aSumUSD = array(0, 0, 0, 0, 0, 0);

// SQL สำหรับข้อมูลรายวัน THB (ทั้งหมด)
$sql = "SELECT 
    COUNT(id) AS total_order,
    delivery_date AS date,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net
FROM bs_orders 
WHERE DATE_FORMAT(delivery_date,'%Y-%m') = '$month'
AND bs_orders.status > 0
GROUP BY delivery_date
ORDER BY delivery_date";

$rst = $dbc->Query($sql);
$dailyData = array();

// เก็บข้อมูลในอาร์เรย์
while ($order = $dbc->Fetch($rst)) {
	$dailyData[] = $order;
	$aSum[0] += $order['total_order'];
	$aSum[1] += $order['amount'];
	$aSum[2] += $order['total'];
	$aSum[3] += $order['vat'];
	$aSum[4] += $order['net'];
}

// SQL สำหรับข้อมูลรายวัน USD
$sql_usd = "SELECT 
    COUNT(id) AS total_order,
    delivery_date AS date,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net,
    SUM(usd) AS usd_amount
FROM bs_orders 
WHERE DATE_FORMAT(delivery_date,'%Y-%m') = '$month'
AND bs_orders.status > 0
AND bs_orders.usd > 0
GROUP BY delivery_date
ORDER BY delivery_date";

$rst_usd = $dbc->Query($sql_usd);
$dailyDataUSD = array();

// เก็บข้อมูล USD ในอาร์เรย์
while ($order = $dbc->Fetch($rst_usd)) {
	$dailyDataUSD[] = $order;
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


	<div class="card mb-4">

		<div class="card-body p-0">
			<table class="table table-sm table-bordered table-striped mb-0">
				<thead class="bg-secondary text-white">
					<tr>
						<th class="text-center">วันที่ส่ง</th>
						<th class="text-center">จำนวนการสั่งซื้อ</th>
						<th class="text-center">จำนวนกิโลกรัม</th>
						<th class="text-center">ยอดรวม</th>
						<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
						<th class="text-center">ยอดรวมสุทธิ</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($dailyData as $order): ?>
						<tr>
							<td class="text-center"><?= date('d/m/Y', strtotime($order['date'])) ?></td>
							<td class="text-center"><?= $order['total_order'] ?></td>
							<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
							<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
							<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
							<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-dark text-white">
					<tr>
						<th class="text-center" colspan="2">รวมทั้งหมด <?= $aSum[0] ?> รายการ</th>
						<th class="text-right pr-2"><?= number_format($aSum[1], 4) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[2], 2) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[3], 2) ?></th>
						<th class="text-right pr-2"><?= number_format($aSum[4], 2) ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<?php if (!empty($dailyDataUSD)): ?>
		<div class="card mb-4">
			<div class="card-header bg-dark text-white">
				<h5 class="mb-0">ยอดขายรายวัน USD</h5>
			</div>
			<div class="card-body p-0">
				<table class="table table-sm table-bordered table-striped mb-0">
					<thead class="bg-dark text-white">
						<tr>
							<th class="text-center">วันที่ส่ง</th>
							<th class="text-center">จำนวนการสั่งซื้อ</th>
							<th class="text-center">จำนวนกิโลกรัม</th>
							<th class="text-center">ยอดรวม (THB)</th>
							<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
							<th class="text-center">ยอดรวมสุทธิ (THB)</th>
							<th class="text-center">ยอดรวม (USD)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($dailyDataUSD as $order): ?>
							<tr>
								<td class="text-center"><?= date('d/m/Y', strtotime($order['date'])) ?></td>
								<td class="text-center"><?= $order['total_order'] ?></td>
								<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
								<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
								<td class="text-right pr-2 text-success"><strong>$<?= number_format($order['usd_amount'], 2) ?></strong></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot class="bg-dark text-white">
						<tr>
							<th class="text-center" colspan="2">รวม USD <?= $aSumUSD[0] ?> รายการ</th>
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
							<td><strong>จำนวนวันที่มีการส่ง:</strong></td>
							<td class="text-right"><?= count($dailyData) ?> วัน</td>
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
							<td><strong>จำนวนวันที่มี USD:</strong></td>
							<td class="text-right"><?= count($dailyDataUSD) ?> วัน</td>
						</tr>
						<tr>
							<td><strong>น้ำหนัก USD:</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[1], 4) ?> กก.</td>
						</tr>
						<tr>
							<td><strong>ยอดรวมสุทธิ (THB):</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[4], 2) ?> บาท</td>
						</tr>
						<tr class="table-success">
							<td><strong>ยอดรวม USD:</strong></td>
							<td class="text-right text-success fw-bold">$<?= number_format($aSumUSD[5], 2) ?></td>
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
							<div class="progress-bar bg-success" style="width: <?= $order_percent ?>%">
								<?= number_format($order_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= $aSumUSD[0] ?> | THB: <?= ($aSum[0] - $aSumUSD[0]) ?> รายการ</small>
					</div>

					<!-- สัดส่วนมูลค่า -->
					<div class="mb-3">
						<small class="text-muted">สัดส่วนมูลค่า (บาท)</small>
						<?php $value_percent = $aSum[4] > 0 ? ($aSumUSD[4] / $aSum[4]) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-success" style="width: <?= $value_percent ?>%">
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
							<div class="progress-bar bg-success" style="width: <?= $weight_percent ?>%">
								<?= number_format($weight_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= number_format($aSumUSD[1], 2) ?> | THB: <?= number_format(($aSum[1] - $aSumUSD[1]), 2) ?> กก.</small>
					</div>

					<!-- สัดส่วนวันที่มีการส่ง -->
					<div>
						<small class="text-muted">สัดส่วนวันที่มี USD</small>
						<?php $day_percent = count($dailyData) > 0 ? (count($dailyDataUSD) / count($dailyData)) * 100 : 0; ?>
						<div class="progress">
							<div class="progress-bar bg-success" style="width: <?= $day_percent ?>%">
								<?= number_format($day_percent, 1) ?>%
							</div>
						</div>
						<small>USD: <?= count($dailyDataUSD) ?> | THB: <?= (count($dailyData) - count($dailyDataUSD)) ?> วัน</small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- สถิติเพิ่มเติม -->
	<div class="row mt-3">
		<div class="col-12">
			<div class="card">
				<div class="card-header bg-dark text-white">
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<div class="text-center">
								<h6>ยอดขายเฉลี่ยต่อวัน</h6>
								<h5 class="text-primary"><?= count($dailyData) > 0 ? number_format($aSum[4] / count($dailyData), 2) : '0.00' ?> บาท</h5>
							</div>
						</div>
						<div class="col-md-3">
							<div class="text-center">
								<h6>รายการเฉลี่ยต่อวัน</h6>
								<h5 class="text-info"><?= count($dailyData) > 0 ? number_format($aSum[0] / count($dailyData), 1) : '0.0' ?> รายการ</h5>
							</div>
						</div>
						<div class="col-md-3">
							<div class="text-center">
								<h6>น้ำหนักเฉลี่ยต่อวัน</h6>
								<h5 class="text-success"><?= count($dailyData) > 0 ? number_format($aSum[1] / count($dailyData), 2) : '0.00' ?> กก.</h5>
							</div>
						</div>
						<div class="col-md-3">
							<div class="text-center">
								<h6>ยอดขายเฉลี่ยต่อรายการ</h6>
								<h5 class="text-warning"><?= $aSum[0] > 0 ? number_format($aSum[4] / $aSum[0], 2) : '0.00' ?> บาท</h5>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>


</body>

</html>