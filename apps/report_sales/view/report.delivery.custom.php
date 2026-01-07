<?php
// เตรียมข้อมูล
$aSum = array(0, 0, 0, 0, 0);
$aSumUSD = array(0, 0, 0, 0, 0, 0); // เพิ่ม index 5 สำหรับ USD amount

// SQL สำหรับข้อมูลการจัดส่ง THB (ทั้งหมด)
$sql = "SELECT
    bs_orders.delivery_date AS date,
    bs_orders.delivery_time AS time,
    bs_orders.code AS order_code,
    bs_deliveries.code AS delivery_code,
    bs_orders.customer_name AS customer_name,
    bs_orders.amount AS amount,
    bs_orders.price AS price,
    bs_orders.total AS total,
    bs_orders.vat AS vat,
    bs_orders.net AS net,
    bs_orders.sales AS sales,
    bs_orders.info_payment AS info_payment,
    bs_deliveries.billing_id AS billing_id,
    bs_deliveries.id AS delivery_id,
    bs_orders.usd AS usd_amount
FROM bs_deliveries 
LEFT JOIN bs_orders ON bs_orders.delivery_id = bs_deliveries.id
WHERE bs_orders.delivery_date BETWEEN '" . $_POST['date_from'] . "' AND '" . $_POST['date_to'] . "'
AND bs_orders.status > 0
ORDER BY bs_orders.delivery_time";

$rst = $dbc->Query($sql);
$allDeliveries = array();

// เก็บข้อมูลทั้งหมด
while ($order = $dbc->Fetch($rst)) {
	$allDeliveries[] = $order;
	$aSum[0] += 1;
	$aSum[1] += $order['amount'];
	$aSum[2] += $order['total'];
	$aSum[3] += $order['vat'];
	$aSum[4] += $order['net'];
}

// แยกข้อมูล USD
$usdDeliveries = array();
foreach ($allDeliveries as $order) {
	if ($order['usd_amount'] > 0) {
		$usdDeliveries[] = $order;
		$aSumUSD[0] += 1;
		$aSumUSD[1] += $order['amount'];
		$aSumUSD[2] += $order['total'];
		$aSumUSD[3] += $order['vat'];
		$aSumUSD[4] += $order['net'];
		$aSumUSD[5] += $order['usd_amount'];
	}
}

// ฟังก์ชันสำหรับหาข้อมูลผู้ขาย
function getEmployeeName($dbc, $emp_id)
{
	if ($emp_id) {
		$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $emp_id);
		return $employee ? $employee['fullname'] : '-';
	}
	return '-';
}

// ฟังก์ชันสำหรับหาข้อมูลผู้ส่ง
function getDrivers($dbc, $delivery_id)
{
	$sql = "SELECT 
        bs_deliveries_drivers.id AS mapping_id,
        bs_deliveries_drivers.truck_type AS truck_type,
        bs_deliveries_drivers.truck_license AS truck_license,
        bs_deliveries_drivers.time_departure AS time_departure,
        bs_deliveries_drivers.time_arrive AS time_arrive,
        bs_employees.fullname AS fullname
    FROM bs_deliveries_drivers 
    LEFT JOIN bs_employees ON bs_deliveries_drivers.emp_driver = bs_employees.id
    WHERE delivery_id = $delivery_id";

	$rst_driver = $dbc->Query($sql);
	$drivers = array();
	while ($driver = $dbc->Fetch($rst_driver)) {
		$drivers[] = $driver['fullname'];
	}
	return $drivers;
}
?>


<style>
	.table th,
	.table td {
		padding: 8px !important;
		font-size: 13px;
	}

	.text-right {
		text-align: right;
	}

	.text-center {
		text-align: center;
	}

	.badge {
		font-size: 11px;
		margin: 1px;
	}

	.card {
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}

	.table-container {
		overflow-x: auto;
	}
</style>


<div class="container-fluid">


	<!-- ตารางการจัดส่งทั้งหมด -->
	<div class="card mb-4">
		<div class="card-header bg-dark text-white">
			<h5 class="mb-0">รายการจัดส่งทั้งหมด</h5>
		</div>
		<div class="card-body p-0">
			<div class="table-container">
				<table class="table table-sm table-bordered table-striped mb-0">
					<thead class="bg-secondary text-white">
						<tr>
							<th class="text-center">วันที่จัดส่ง</th>
							<th class="text-center">เวลาจัดส่ง</th>
							<th class="text-center">หมายเลขสั่งซื้อ</th>
							<th class="text-center">หมายเลขจัดส่ง</th>
							<th class="text-center">ลูกค้า</th>
							<th class="text-center">จำนวน (กก.)</th>
							<th class="text-center">บาท/กิโล</th>
							<th class="text-center">ยอดรวม</th>
							<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
							<th class="text-center">ยอดรวมสุทธิ</th>
							<th class="text-center">ผู้ขาย</th>
							<th class="text-center">หมายเลขบิล</th>
							<th class="text-center">ผู้ส่ง</th>
							<th class="text-center">วิธีชำระเงิน</th>
							<th class="text-center">Clearing</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($allDeliveries as $order): ?>
							<tr>
								<td class="text-center"><?= $order['date'] ?></td>
								<td class="text-center"><?= $order['time'] ?></td>
								<td class="text-center"><?= $order['order_code'] ?></td>
								<td class="text-center"><?= $order['delivery_code'] ?></td>
								<td class="text-center"><?= htmlspecialchars($order['customer_name']) ?></td>
								<td class="text-right"><?= number_format($order['amount'], 4) ?></td>
								<td class="text-right"><?= number_format($order['price'], 2) ?></td>
								<td class="text-right"><?= number_format($order['total'], 2) ?></td>
								<td class="text-right"><?= number_format($order['vat'], 2) ?></td>
								<td class="text-right"><?= number_format($order['net'], 2) ?></td>
								<td class="text-center"><?= getEmployeeName($dbc, $order['sales']) ?></td>
								<td class="text-center"><?= $order['billing_id'] ?></td>
								<td class="text-left">
									<?php
									$drivers = getDrivers($dbc, $order['delivery_id']);
									foreach ($drivers as $driver):
									?>
										<span class="badge bg-dark text-white"><?= htmlspecialchars($driver) ?></span>
									<?php endforeach; ?>
								</td>
								<td class="text-center"><?= htmlspecialchars($order['info_payment']) ?></td>
								<td class="text-center">-</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot class="bg-dark text-white">
						<tr>
							<th class="text-center" colspan="5">รวมทั้งหมด <?= $aSum[0] ?> รายการ</th>
							<th class="text-right"><?= number_format($aSum[1], 4) ?></th>
							<th></th>
							<th class="text-right"><?= number_format($aSum[2], 2) ?></th>
							<th class="text-right"><?= number_format($aSum[3], 2) ?></th>
							<th class="text-right"><?= number_format($aSum[4], 2) ?></th>
							<th class="text-center" colspan="5"></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>

	<!-- ตารางการจัดส่ง USD -->
	<?php if (!empty($usdDeliveries)): ?>
		<div class="card mb-4">
			<div class="card-header ">
				<h5 class="mb-0">รายการจัดส่ง USD</h5>
			</div>
			<div class="card-body p-0">
				<div class="table-container">
					<table class="table table-sm table-bordered table-striped mb-0">
						<thead class="bg-dark text-white">
							<tr>
								<th class="text-center">วันที่จัดส่ง</th>
								<th class="text-center">เวลาจัดส่ง</th>
								<th class="text-center">หมายเลขสั่งซื้อ</th>
								<th class="text-center">หมายเลขจัดส่ง</th>
								<th class="text-center">ลูกค้า</th>
								<th class="text-center">จำนวน (กก.)</th>
								<th class="text-center">บาท/กิโล</th>
								<th class="text-center">ยอดรวม (THB)</th>
								<th class="text-center">ภาษีมูลค่าเพิ่ม</th>
								<th class="text-center">ยอดรวมสุทธิ (THB)</th>
								<th class="text-center">ยอดรวม (USD)</th>
								<th class="text-center">ผู้ขาย</th>
								<th class="text-center">หมายเลขบิล</th>
								<th class="text-center">ผู้ส่ง</th>
								<th class="text-center">วิธีชำระเงิน</th>
								<th class="text-center">Clearing</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($usdDeliveries as $order): ?>
								<tr>
									<td class="text-center"><?= $order['date'] ?></td>
									<td class="text-center"><?= $order['time'] ?></td>
									<td class="text-center"><?= $order['order_code'] ?></td>
									<td class="text-center"><?= $order['delivery_code'] ?></td>
									<td class="text-center"><?= htmlspecialchars($order['customer_name']) ?></td>
									<td class="text-right"><?= number_format($order['amount'], 4) ?></td>
									<td class="text-right"><?= number_format($order['price'], 2) ?></td>
									<td class="text-right"><?= number_format($order['total'], 2) ?></td>
									<td class="text-right"><?= number_format($order['vat'], 2) ?></td>
									<td class="text-right"><?= number_format($order['net'], 2) ?></td>
									<td class="text-right text-success"><strong>$<?= number_format($order['usd_amount'], 2) ?></strong></td>
									<td class="text-center"><?= getEmployeeName($dbc, $order['sales']) ?></td>
									<td class="text-center"><?= $order['billing_id'] ?></td>
									<td class="text-left">
										<?php
										$drivers = getDrivers($dbc, $order['delivery_id']);
										foreach ($drivers as $driver):
										?>
											<span class="badge bg-dark  text-white"><?= htmlspecialchars($driver) ?></span>
										<?php endforeach; ?>
									</td>
									<td class="text-center"><?= htmlspecialchars($order['info_payment']) ?></td>
									<td class="text-center">-</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot class="bg-dark text-white">
							<tr>
								<th class="text-center" colspan="5">รวม USD <?= $aSumUSD[0] ?> รายการ</th>
								<th class="text-right"><?= number_format($aSumUSD[1], 4) ?></th>
								<th></th>
								<th class="text-right"><?= number_format($aSumUSD[2], 2) ?></th>
								<th class="text-right"><?= number_format($aSumUSD[3], 2) ?></th>
								<th class="text-right"><?= number_format($aSumUSD[4], 2) ?></th>
								<th class="text-right"><strong>$<?= number_format($aSumUSD[5], 2) ?></strong></th>
								<th class="text-center" colspan="5"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- สรุปเปรียบเทียบ -->
	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h6 class="mb-0">สรุปการจัดส่งรวม</h6>
				</div>
				<div class="card-body">
					<table class="table table-sm mb-0">
						<tr>
							<td><strong>จำนวนรายการทั้งหมด:</strong></td>
							<td class="text-right"><?= number_format($aSum[0]) ?> รายการ</td>
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
							<td class="text-right font-weight-bold"><?= number_format(($aSum[4] - $aSumUSD[4]), 2) ?> บาท</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h6 class="mb-0">สรุปการจัดส่ง USD</h6>
				</div>
				<div class="card-body">
					<table class="table table-sm mb-0">
						<tr>
							<td><strong>จำนวนรายการ USD:</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[0]) ?> รายการ</td>
						</tr>
						<tr>
							<td><strong>น้ำหนัก USD:</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[1], 4) ?> กก.</td>
						</tr>
						<tr>
							<td><strong>ยอดรวมสุทธิ (THB):</strong></td>
							<td class="text-right"><?= number_format($aSumUSD[4], 2) ?> บาท</td>
						</tr>
						<tr class="table-info">
							<td><strong>ยอดรวม USD:</strong></td>
							<td class="text-right text-dark font-weight-bold">$<?= number_format($aSumUSD[5], 2) ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>



	</div>


	</body>

	</html>