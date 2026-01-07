<?php
// เตรียมข้อมูล
$year = $_POST['year'] ?? date('Y');
$aSum = array(0, 0, 0, 0, 0);
$aSumUSD = array(0, 0, 0, 0, 0, 0); // เพิ่ม index 5 สำหรับ USD amount

// SQL สำหรับข้อมูลรายเดือน (THB)
$sql = "SELECT
    COUNT(id) AS total_order,
    MONTH(date) AS month,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net,
    DATE_FORMAT(date,'%Y-%m') AS sql_month
FROM bs_orders 
WHERE YEAR(date) = '$year' 
AND bs_orders.parent IS NULL  
AND bs_orders.status > -1
GROUP BY MONTH(date)
ORDER BY month";

$rst = $dbc->Query($sql);
$monthlyData = array();

// เก็บข้อมูลในอาร์เรย์
while ($order = $dbc->Fetch($rst)) {
	$monthlyData[] = $order;
	$aSum[0] += $order['total_order'];
	$aSum[1] += $order['amount'];
	$aSum[2] += $order['total'];
	$aSum[3] += $order['vat'];
	$aSum[4] += $order['net'];
}

// SQL สำหรับข้อมูลรายเดือน (USD)
$sql_usd = "SELECT
    COUNT(id) AS total_order,
    MONTH(date) AS month,
    SUM(amount) AS amount,
    SUM(price) AS price,
    SUM(total) AS total,
    SUM(vat) AS vat,
    SUM(net) AS net,
    SUM(usd) AS usd_amount,
    DATE_FORMAT(date,'%Y-%m') AS sql_month
FROM bs_orders 
WHERE YEAR(date) = '$year' 
AND bs_orders.parent IS NULL  
AND bs_orders.status > -1
AND bs_orders.usd > 0
GROUP BY MONTH(date)
ORDER BY month";

$rst_usd = $dbc->Query($sql_usd);
$monthlyDataUSD = array();

// เก็บข้อมูล USD ในอาร์เรย์
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

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>รายงานยอดขาย <?= $year ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.toggle-btn {
			cursor: pointer;
		}

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

		/* d-none class for Bootstrap compatibility */
		.d-none {
			display: none !important;
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
</head>

<body>

	<div class="container-fluid">

		<!-- หัวข้อหลัก -->
		<div class="d-flex justify-content-between align-items-center mb-3">
			<div class="fs-6">
				รวมทั้งสิ้น <?= number_format($aSum[0]) ?> รายการ
			</div>
		</div>

		<!-- ตารางหลัก THB -->
		<table class="table table-sm table-bordered table-striped">
			<thead class="bg-dark">
				<tr>
					<td class="text-center  text-white font-weight-bold"></td>
					<td class="text-center text-white font-weight-bold">MONTHLY</td>
					<th class="text-center text-white font-weight-bold">ORDER</th>
					<th class="text-center text-white font-weight-bold">KGS.</th>
					<th class="text-center text-white font-weight-bold">PRICE</th>
					<th class="text-center text-white font-weight-bold">VAT</th>
					<th class="text-center text-white font-weight-bold">TOTAL</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach ($monthlyData as $index => $order): ?>

					<!-- แถวสรุปรายเดือน -->
					<tr class="text-dark">
						<td>
							<button class="btn btn-sm btn-primary toggle-btn">
								Toggle
							</button>
						</td>
						<td class="text-center"><?= $order['sql_month'] ?></td>
						<td class="text-center"><?= $order['total_order'] ?></td>
						<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
						<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
						<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
						<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
					</tr>

					<!-- แถวรายละเอียด -->
					<tr class="d-none" id="details-<?= $index ?>">
						<td colspan="7">

							<!-- ตารางรายละเอียด -->
							<table class="table table-sm table-bordered table-striped">
								<thead class="bg-dark">
									<tr>
										<th class="text-center text-white font-weight-bold">DATE ADD</th>
										<th class="text-center text-white font-weight-bold">ORDER NO.</th>
										<th class="text-center text-white font-weight-bold">INVOICE NO.</th>
										<th class="text-center text-white font-weight-bold">CUSTOMER</th>
										<th class="text-center text-white font-weight-bold">KGS.</th>
										<th class="text-center text-white font-weight-bold">BATH / KGS.</th>
										<th class="text-center text-white font-weight-bold">PRICE</th>
										<th class="text-center text-white font-weight-bold">VAT</th>
										<th class="text-center text-white font-weight-bold">TOTAL</th>
										<th class="text-center text-white font-weight-bold">DELIVERY DATE</th>
										<th class="text-center text-white font-weight-bold">SALES</th>
									</tr>
								</thead>
								<tbody>
									<?php
									// SQL สำหรับรายละเอียดรายวัน
									$sql_daily = "SELECT 
                                bs_orders.id, bs_orders.code, bs_orders.customer_id,
                                bs_orders.customer_name, bs_orders.date, bs_orders.sales, bs_orders.user, bs_orders.parent,
                                bs_orders.amount, bs_orders.price, bs_orders.vat_type, bs_orders.vat, bs_orders.total, bs_orders.net, 
                                bs_orders.delivery_date, bs_orders.delivery_time, bs_orders.status, bs_deliveries.billing_id
                            FROM bs_orders
                            LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id 
                            WHERE DATE_FORMAT(bs_orders.date,'%Y-%m') = '{$order['sql_month']}' 
                            AND bs_orders.status > -1
                            ORDER BY bs_orders.date";

									$rst_daily = $dbc->Query($sql_daily);
									while ($item = $dbc->Fetch($rst_daily)):

										// หาชื่อเซลล์
										$employee_name = "-";
										if ($item['sales'] != null) {
											$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $item['sales']);
											if (is_array($employee)) {
												$employee_name = $employee['fullname'];
											}
										}
									?>
										<tr>
											<td class="text-center"><?= $item['date'] ?></td>
											<td class="text-center"><?= $item['code'] ?></td>
											<td class="text-center"><?= $item['billing_id'] ?></td>
											<td class="text-center"><?= htmlspecialchars($item['customer_name']) ?></td>
											<td class="text-right"><?= number_format($item['amount'], 4) ?></td>
											<td class="text-right"><?= number_format($item['price'], 2) ?></td>
											<td class="text-right"><?= number_format($item['total'], 2) ?></td>
											<td class="text-right"><?= number_format($item['vat'], 2) ?></td>
											<td class="text-right"><?= number_format($item['net'], 2) ?></td>
											<td class="text-center"><?= $item['delivery_date'] ?></td>
											<td class="text-center"><?= htmlspecialchars($employee_name) ?></td>
										</tr>
									<?php endwhile; ?>
								</tbody>
							</table>

						</td>
					</tr>

				<?php endforeach; ?>

			</tbody>

			<!-- ส่วนรวม -->
			<tfoot class="bg-dark">
				<tr>
					<th class="text-center text-white" colspan="3">
						รวมทั้งหมด <?= number_format($aSum[0]) ?> รายการ
					</th>
					<th class="text-right pr-2 text-white"><?= number_format($aSum[1], 4) ?></th>
					<th class="text-right pr-2 text-white"><?= number_format($aSum[2], 2) ?></th>
					<th class="text-right pr-2 text-white"><?= number_format($aSum[3], 2) ?></th>
					<th class="text-right pr-2 text-white"><?= number_format($aSum[4], 2) ?></th>
				</tr>
			</tfoot>
		</table>

		<!-- ตารางขายเป็น USD -->
		<?php if (!empty($monthlyDataUSD)): ?>
			<div class="mt-4">
				<h5>รายงานยอดขาย USD</h5>

				<table class="table table-sm table-bordered table-striped">
					<thead class="bg-dark">
						<tr>
							<td class="text-center text-white"></td>
							<td class="text-center text-white font-weight-bold">MONTHLY</td>
							<th class="text-center text-white font-weight-bold">ORDER</th>
							<th class="text-center text-white font-weight-bold">KGS.</th>
							<th class="text-center text-white font-weight-bold">PRICE (THB)</th>
							<th class="text-center text-white font-weight-bold">VAT</th>
							<th class="text-center text-white font-weight-bold">TOTAL (THB)</th>
							<th class="text-center text-white font-weight-bold">USD</th>
						</tr>
					</thead>
					<tbody>

						<?php foreach ($monthlyDataUSD as $index => $order): ?>

							<!-- แถวสรุปรายเดือน USD -->
							<tr class="text-dark">
								<td>
									<button class="btn btn-sm btn-success toggle-btn">
										Toggle
									</button>
								</td>
								<td class="text-center"><?= $order['sql_month'] ?></td>
								<td class="text-center"><?= $order['total_order'] ?></td>
								<td class="text-right pr-2"><?= number_format($order['amount'], 4) ?></td>
								<td class="text-right pr-2"><?= number_format($order['total'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['vat'], 2) ?></td>
								<td class="text-right pr-2"><?= number_format($order['net'], 2) ?></td>
								<td class="text-right pr-2 text-success"><strong>$<?= number_format($order['usd_amount'], 2) ?></strong></td>
							</tr>

							<!-- แถวรายละเอียด USD -->
							<tr class="d-none" id="details-usd-<?= $index ?>">
								<td colspan="8">

									<!-- ตารางรายละเอียด USD -->
									<table class="table table-sm table-bordered table-striped">
										<thead class="bg-success">
											<tr>
												<th class="text-center  text-white font-weight-bold">DATE ADD</th>
												<th class="text-center text-white font-weight-bold">ORDER NO.</th>
												<th class="text-center text-white font-weight-bold">INVOICE NO.</th>
												<th class="text-center text-white font-weight-bold">CUSTOMER</th>
												<th class="text-center text-white font-weight-bold">KGS.</th>
												<th class="text-center text-white font-weight-bold">BATH / KGS.</th>
												<th class="text-center text-white font-weight-bold">PRICE (THB)</th>
												<th class="text-center text-white font-weight-bold">VAT</th>
												<th class="text-center text-white font-weight-bold">TOTAL (THB)</th>
												<th class="text-center text-white font-weight-bold">USD</th>
												<th class="text-center text-white font-weight-bold">DELIVERY DATE</th>
												<th class="text-center text-white font-weight-bold">SALES</th>
											</tr>
										</thead>
										<tbody>
											<?php
											// SQL สำหรับรายละเอียดรายวัน USD
											$sql_daily_usd = "SELECT 
                                    bs_orders.id, bs_orders.code, bs_orders.customer_id,
                                    bs_orders.customer_name, bs_orders.date, bs_orders.sales, bs_orders.user, bs_orders.parent,
                                    bs_orders.amount, bs_orders.price, bs_orders.vat_type, bs_orders.vat, bs_orders.total, bs_orders.net, 
                                    bs_orders.delivery_date, bs_orders.delivery_time, bs_orders.status, bs_orders.usd,
                                    bs_deliveries.billing_id
                                FROM bs_orders
                                LEFT OUTER JOIN bs_deliveries ON bs_orders.delivery_id = bs_deliveries.id 
                                WHERE DATE_FORMAT(bs_orders.date,'%Y-%m') = '{$order['sql_month']}' 
                                AND bs_orders.status > -1
                                AND bs_orders.usd > 0
                                ORDER BY bs_orders.date";

											$rst_daily_usd = $dbc->Query($sql_daily_usd);
											while ($item = $dbc->Fetch($rst_daily_usd)):

												// หาชื่อเซลล์
												$employee_name = "-";
												if ($item['sales'] != null) {
													$employee = $dbc->GetRecord("bs_employees", "*", "id=" . $item['sales']);
													if (is_array($employee)) {
														$employee_name = $employee['fullname'];
													}
												}
											?>
												<tr>
													<td class="text-center"><?= $item['date'] ?></td>
													<td class="text-center"><?= $item['code'] ?></td>
													<td class="text-center"><?= $item['billing_id'] ?></td>
													<td class="text-center"><?= htmlspecialchars($item['customer_name']) ?></td>
													<td class="text-right"><?= number_format($item['amount'], 4) ?></td>
													<td class="text-right"><?= number_format($item['price'], 2) ?></td>
													<td class="text-right"><?= number_format($item['total'], 2) ?></td>
													<td class="text-right"><?= number_format($item['vat'], 2) ?></td>
													<td class="text-right"><?= number_format($item['net'], 2) ?></td>
													<td class="text-right text-success"><strong>$<?= number_format($item['usd'], 2) ?></strong></td>
													<td class="text-center"><?= $item['delivery_date'] ?></td>
													<td class="text-center"><?= htmlspecialchars($employee_name) ?></td>
												</tr>
											<?php endwhile; ?>
										</tbody>
									</table>

								</td>
							</tr>

						<?php endforeach; ?>

					</tbody>

					<!-- ส่วนรวม USD -->
					<tfoot class="bg-dark">
						<tr>
							<th class="text-center text-white" colspan="3">
								รวม USD <?= number_format($aSumUSD[0]) ?> รายการ
							</th>
							<th class="text-right pr-2 text-white"><?= number_format($aSumUSD[1], 4) ?></th>
							<th class="text-right pr-2 text-white"><?= number_format($aSumUSD[2], 2) ?></th>
							<th class="text-right pr-2 text-white"><?= number_format($aSumUSD[3], 2) ?></th>
							<th class="text-right pr-2 text-white"><?= number_format($aSumUSD[4], 2) ?></th>
							<th class="text-right pr-2 text-white"><strong>$<?= number_format($aSumUSD[5], 2) ?></strong></th>
						</tr>
					</tfoot>
				</table>
			</div>
		<?php endif; ?>

		<!-- สรุปเปรียบเทียบ -->
		<div class="mt-4">
			<div class="row">
				<div class="col-md-6">
					<div class="card">
						<div class="card-header bg-dark text-white">
							<h5 class="mb-0">สรุปยอดขายรวม</h5>
						</div>
						<div class="card-body">
							<table class="table table-sm">
								<tr>
									<td><strong>จำนวนรายการทั้งหมด:</strong></td>
									<td class="text-right"><?= number_format($aSum[0]) ?> รายการ</td>
								</tr>
								<tr>
									<td><strong>ยอดรวม (TOTAL + VAT) ทั้งหมด:</strong></td>
									<td class="text-right"><?= number_format($aSum[4], 2) ?> บาท</td>
								</tr>
								<tr class="table-info">
									<td><strong>ยอดรวม THB :</strong></td>
									<td class="text-right fw-bold"><?= number_format(($aSum[4] - $aSumUSD[4]), 2) ?> บาท</td>
								</tr>
								<tr>
									<td><strong>น้ำหนักรวม:</strong></td>
									<td class="text-right"><?= number_format($aSum[1], 4) ?> กก.</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card">
						<div class="card-header bg-dark text-white">
							<h5 class="mb-0">สรุปยอดขาย USD</h5>
						</div>
						<div class="card-body">
							<table class="table table-sm">
								<tr>
									<td><strong>จำนวนรายการ USD:</strong></td>
									<td class="text-right"><?= number_format($aSumUSD[0]) ?> รายการ</td>
								</tr>
								<tr>
									<td><strong>ยอดรวม (TOTAL + VAT):</strong></td>
									<td class="text-right"><?= number_format($aSumUSD[4], 2) ?> บาท</td>
								</tr>
								<tr class="table-success">
									<td><strong>ยอดรวม USD:</strong></td>
									<td class="text-right text-success fw-bold">$<?= number_format($aSumUSD[5], 2) ?></td>
								</tr>
								<tr>
									<td><strong>น้ำหนักรวม:</strong></td>
									<td class="text-right"><?= number_format($aSumUSD[1], 4) ?> กก.</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>


		</div>

		<script>
			$(document).ready(function() {
				// จัดการปุ่ม Toggle แบบเดิมสำหรับทั้งตาราง THB และ USD
				$('.toggle-btn').click(function() {
					$(this).parent().parent().next().toggleClass('d-none');

					// เปลี่ยนข้อความปุ่มและสี
					if ($(this).text() === 'Toggle') {
						$(this).text('Hide');
						if ($(this).hasClass('btn-primary')) {
							$(this).removeClass('btn-primary').addClass('btn-secondary');
						} else if ($(this).hasClass('btn-success')) {
							$(this).removeClass('btn-success').addClass('btn-warning');
						}
					} else {
						$(this).text('Toggle');
						if ($(this).hasClass('btn-secondary')) {
							$(this).removeClass('btn-secondary').addClass('btn-primary');
						} else if ($(this).hasClass('btn-warning')) {
							$(this).removeClass('btn-warning').addClass('btn-success');
						}
					}
				});

				// เพิ่ม tooltip สำหรับปุ่ม
				$('.toggle-btn').attr('title', 'คลิกเพื่อดูรายละเอียด');
			});
		</script>

</body>

</html>