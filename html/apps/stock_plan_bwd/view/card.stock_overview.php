<?php

class LuckGemsStockReportManager
{
	private $dbc;
	private $reportDisplayStartDate;
	private $reportDisplayEndDate;
	private $productTypeIds;
	private $debug = true;

	const SECONDS_IN_DAY = 86400;
	const DATA_START_DATE = "2023-01-01";
	const DAYS_FORWARD_FROM_DISPLAY_START = 20;

	public function __construct($dbc)
	{
		$this->dbc = $dbc;
		$this->productTypeIds = range(1, 12);
		$this->initializeDates();

		if (!$this->dbc) {
			$this->debugLog("ERROR: Database connection is null during __construct.");
			throw new Exception("Database connection object is required.");
		}
	}

	private function debugLog($message)
	{
		if ($this->debug) {
			error_log("LuckGemsReport Debug: " . $message);
			echo "<!-- LuckGemsReport Debug: " . htmlspecialchars($message) . " -->\n";
		}
	}

	private function initializeDates()
	{
		$this->reportDisplayStartDate = strtotime("yesterday");
		$this->reportDisplayEndDate = $this->reportDisplayStartDate + (self::DAYS_FORWARD_FROM_DISPLAY_START * self::SECONDS_IN_DAY);

		if ($this->reportDisplayStartDate > $this->reportDisplayEndDate) {
			$this->debugLog("WARNING: Report display start date (" . date('Y-m-d', $this->reportDisplayStartDate) .
				") is after calculated report display end date (" . date('Y-m-d', $this->reportDisplayEndDate) .
				"). Adjusting display range to be at least one day by setting start = end.");
			$this->reportDisplayStartDate = $this->reportDisplayEndDate;
		}

		$this->debugLog("Report Display Dates initialized - Start: " . date('Y-m-d', $this->reportDisplayStartDate) .
			", End: " . date('Y-m-d', $this->reportDisplayEndDate));
	}

	private function getProducts()
	{
		$this->debugLog("Fetching products...");
		$products = array();
		if (empty($this->productTypeIds)) {
			$this->debugLog("No product type IDs specified. Returning empty array.");
			return array();
		}
		$productIdsString = implode(',', $this->productTypeIds);

		try {
			$sql = "SELECT id, name FROM bs_products_type WHERE id IN (" . $productIdsString . ") ORDER BY FIELD(id, " . $productIdsString . ")";
			$rst = $this->dbc->Query($sql);
			while ($row = $this->dbc->Fetch($rst)) {
				$products[] = $row;
			}
			$this->debugLog("Total products found: " . count($products));
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching products: " . $e->getMessage());
		}
		return $products;
	}

	private function fetchAllLuckGemsData()
	{
		$this->debugLog("Fetching all LuckGems data in bulk...");
		$data = array(
			'luckgems_in' => array(),
			'luckgems_out' => array()
		);

		if (empty($this->productTypeIds)) {
			$this->debugLog("No product type IDs specified for bulk data fetch. Returning empty data.");
			return $data;
		}
		$productIdsString = implode(',', $this->productTypeIds);

		$fullStartDate = date("Y-m-d", strtotime(self::DATA_START_DATE));
		$fullEndDate = date("Y-m-d", $this->reportDisplayEndDate);

		$this->debugLog("Bulk data fetch date range: " . $fullStartDate . " to " . $fullEndDate);

		try {
			$sqlLuckGemsIn = "SELECT product_type, date, SUM(amount) AS total_amount
                              FROM bs_stock_adjusted_bwd
                              WHERE product_type IN (" . $productIdsString . ")
                              AND type_id = 9
                              AND date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                              GROUP BY product_type, date";
			$rst = $this->dbc->Query($sqlLuckGemsIn);
			while ($row = $this->dbc->Fetch($rst)) {
				$data['luckgems_in'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
			}
			$this->debugLog("Fetched " . count($data['luckgems_in']) . " distinct LuckGems in date entries.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching LuckGems in data: " . $e->getMessage());
		}

		try {
			$sqlLuckGemsOut = "SELECT product_type, delivery_date AS date, SUM(amount) AS total_amount
                               FROM bs_orders_bwd
                               WHERE product_type IN (" . $productIdsString . ")
                               AND status > 0
                               AND LOWER(TRIM(platform)) = 'luckgems'
                               AND delivery_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                               GROUP BY product_type, delivery_date";
			$this->debugLog("LuckGems Out Query: " . $sqlLuckGemsOut);
			$rst = $this->dbc->Query($sqlLuckGemsOut);
			$luckGemsOutCount = 0;
			while ($row = $this->dbc->Fetch($rst)) {
				$data['luckgems_out'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
				$luckGemsOutCount++;
				$this->debugLog("LuckGems Out Found: Date=" . $row['date'] . ", Product=" . $row['product_type'] . ", Amount=" . $row['total_amount']);
			}
			$this->debugLog("Fetched " . $luckGemsOutCount . " distinct LuckGems out date entries from date range " . $fullStartDate . " to " . $fullEndDate);
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching LuckGems out data: " . $e->getMessage());
		}

		return $data;
	}

	private function calculateInitialBalance($allLuckGemsData)
	{
		$this->debugLog("Calculating initial LuckGems balance from bulk data...");
		$balances = array_fill_keys($this->productTypeIds, 0);

		$initialBalanceEndDate = date("Y-m-d", $this->reportDisplayStartDate - self::SECONDS_IN_DAY);
		$this->debugLog("Initial Balance will be calculated up to: " . $initialBalanceEndDate);

		foreach ($this->productTypeIds as $productTypeId) {
			$balance = 0;
			for ($time = strtotime(self::DATA_START_DATE); $time <= strtotime($initialBalanceEndDate); $time += self::SECONDS_IN_DAY) {
				$date = date("Y-m-d", $time);

				$dailyIn = isset($allLuckGemsData['luckgems_in'][$date][$productTypeId]) ? $allLuckGemsData['luckgems_in'][$date][$productTypeId] : 0;
				$dailyOut = isset($allLuckGemsData['luckgems_out'][$date][$productTypeId]) ? $allLuckGemsData['luckgems_out'][$date][$productTypeId] : 0;

				$balance += $dailyIn;
				$balance -= $dailyOut;
			}
			$balances[$productTypeId] = $balance;
			$this->debugLog("Final Initial LuckGems Balance for product " . $productTypeId . ": " . number_format($balance, 4));
		}

		return $balances;
	}

	private function generateDateRange()
	{
		$this->debugLog("Generating date range for report display...");
		$dates = array();
		for ($time = $this->reportDisplayStartDate; $time <= $this->reportDisplayEndDate; $time += self::SECONDS_IN_DAY) {
			$dates[] = $time;
		}
		$this->debugLog("Generated " . count($dates) . " dates for report display.");
		return $dates;
	}

	private function calculateDailyMovements($dates, $initialBalance, $allLuckGemsData)
	{
		$this->debugLog("Calculating daily LuckGems movements from bulk data...");
		$data = array(
			'balance_forward' => array_fill(0, count($this->productTypeIds), array()),
			'luckgems_in' => array_fill(0, count($this->productTypeIds), array()),
			'luckgems_out' => array_fill(0, count($this->productTypeIds), array()),
			'carry_forward' => array_fill(0, count($this->productTypeIds), array()),
			'daily_total_in' => array(),
			'daily_total_out' => array()
		);

		$luckGemsBalance = $initialBalance;

		foreach ($dates as $dateIndex => $dateTimestamp) {
			$currentDate = date("Y-m-d", $dateTimestamp);
			$dailyTotalIn = 0;
			$dailyTotalOut = 0;

			foreach ($this->productTypeIds as $productIndex => $productTypeId) {
				$data['balance_forward'][$productIndex][] = $luckGemsBalance[$productTypeId];

				$dailyIn = isset($allLuckGemsData['luckgems_in'][$currentDate][$productTypeId]) ? $allLuckGemsData['luckgems_in'][$currentDate][$productTypeId] : 0;
				$data['luckgems_in'][$productIndex][] = $dailyIn;
				$luckGemsBalance[$productTypeId] += $dailyIn;
				$dailyTotalIn += $dailyIn;

				$dailyOut = isset($allLuckGemsData['luckgems_out'][$currentDate][$productTypeId]) ? $allLuckGemsData['luckgems_out'][$currentDate][$productTypeId] : 0;
				$data['luckgems_out'][$productIndex][] = $dailyOut;
				$luckGemsBalance[$productTypeId] -= $dailyOut;
				$dailyTotalOut += $dailyOut;

				$data['carry_forward'][$productIndex][] = $luckGemsBalance[$productTypeId];
			}
			$data['daily_total_in'][] = $dailyTotalIn;
			$data['daily_total_out'][] = $dailyTotalOut;
		}

		return $data;
	}

	private function getDayColorStyle($date)
	{
		$dayNumber = date("w", $date);
		$dayColors = [
			'0' => '#ffebee',    // วันอาทิตย์ - สีแดงอ่อน
			'1' => '#fffde7',    // วันจันทร์ - สีเหลืองอ่อน
			'2' => '#fce4ec',    // วันอังคาร - สีชมพูอ่อน
			'3' => '#e8f5e8',    // วันพุธ - สีเขียวอ่อน
			'4' => '#fff3e0',    // วันพฤหัสบดี - สีส้มอ่อน
			'5' => '#e1f5fe',    // วันศุกร์ - สีฟ้าอ่อน
			'6' => '#f3e5f5'     // วันเสาร์ - สีม่วงอ่อน
		];

		return isset($dayColors[$dayNumber]) ? $dayColors[$dayNumber] : '#ffffff';
	}

	public function generateReport()
	{
		try {
			$products = $this->getProducts();
			if (empty($products)) {
				throw new Exception("No products found for the specified IDs.");
			}

			$allLuckGemsData = $this->fetchAllLuckGemsData();
			$initialBalance = $this->calculateInitialBalance($allLuckGemsData);
			$dates = $this->generateDateRange();
			$movements = $this->calculateDailyMovements($dates, $initialBalance, $allLuckGemsData);

			return array(
				'products' => $products,
				'dates' => $dates,
				'movements' => $movements,
				'product_type_ids' => $this->productTypeIds
			);
		} catch (Exception $e) {
			$this->debugLog("FATAL ERROR in generateReport: " . $e->getMessage());
			throw $e;
		}
	}

	public function renderReport()
	{
		try {
			$reportData = $this->generateReport();
			ob_start();
?>

			<!DOCTYPE html>
			<html lang="th">

			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>ระบบจัดการสต็อก LuckGems</title>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
				<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
				<style>
					body {
						background: white;
						font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
						min-height: 100vh;
					}

					.main-container {
						background: white;
						margin: 0;
						overflow: hidden;
					}

					.controls-section {
						padding: 20px 30px;
						background: #f8f9fa;
						border-bottom: 1px solid #dee2e6;
					}

					.summary-cards {
						display: grid;
						grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
						gap: 15px;
						margin-bottom: 20px;
					}

					.summary-card {
						background: white;
						padding: 20px;
						border-radius: 15px;
						box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
						text-align: center;
						transition: transform 0.3s ease;
					}

					.summary-card:hover {
						transform: translateY(-5px);
					}

					.summary-card .icon {
						font-size: 2rem;
						margin-bottom: 10px;
					}

					.summary-card .value {
						font-size: 1.5rem;
						font-weight: bold;
						margin-bottom: 5px;
					}

					.summary-card .label {
						color: #6c757d;
						font-size: 0.9rem;
					}

					.table-container {
						padding: 30px;
						overflow-x: auto;
					}

					.table-stock {
						border-collapse: separate;
						border-spacing: 0;
						background: white;
						border-radius: 15px;
						overflow: hidden;
						box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
					}

					.table-stock th,
					.table-stock td {
						border: 1px solid #e9ecef;
						padding: 12px 8px;
						text-align: center;
						vertical-align: middle;
						font-size: 0.9rem;
						transition: all 0.3s ease;
					}

					.table-stock th {
						font-weight: 600;
						text-transform: uppercase;
						letter-spacing: 1px;
					}

					.date-header {
						padding: 20px 8px !important;
						font-weight: bold;
						font-size: 0.8rem;
					}

					.product-header {
						color: #2d3436;
						font-weight: bold;
						transition: none !important;
						font-size: 0.8rem;
					}

					.row-label {
						background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
						color: white;
						font-weight: bold;
						text-align: right !important;
						padding-right: 15px !important;
						min-width: 150px;
					}

					.row-label.white-bg {
						background: white !important;
						color: #333 !important;
						border: 1px solid #e9ecef;
					}

					.positive-value {
						color: #27ae60;
						font-weight: bold;
					}

					.negative-value {
						color: #e74c3c;
						font-weight: bold;
					}

					.zero-value {
						color: #95a5a6;
					}

					.fade-in {
						animation: fadeIn 0.5s ease-in;
					}

					@keyframes fadeIn {
						from {
							opacity: 0;
							transform: translateY(20px);
						}

						to {
							opacity: 1;
							transform: translateY(0);
						}
					}

					.today {
						border: 2px solid #007bff;
					}

					@media (max-width: 768px) {

						.table-stock th,
						.table-stock td {
							padding: 8px 4px;
							font-size: 0.8rem;
						}

						.date-header {
							padding: 10px 4px !important;
						}
					}
				</style>
			</head>

			<body>

				<div class="main-container luckgems-container">
					<div class="controls-section">
						<div class="d-flex justify-content-between align-items-center">
							<div class="text-muted">
								<i class="fas fa-gem me-2"></i>
								LuckGems Stock Report
							</div>
							<div class="text-muted">
								<i class="fas fa-calendar-alt me-2"></i>
								ช่วงวันที่: <?php echo date("d/m/Y", $reportData['dates'][0]) . " - " . date("d/m/Y", end($reportData['dates'])); ?>
								<span class="ms-3">
									<i class="fas fa-clock me-2"></i>
									อัพเดทล่าสุด: <?php echo date("d/m/Y H:i:s"); ?>
								</span>
							</div>
						</div>

						<div class="summary-cards mt-3" id="summaryCards">
							<?php
							$totalProducts = count($this->productTypeIds);
							$totalStock = array_sum(array_map(function ($arr) {
								return end($arr);
							}, $reportData['movements']['carry_forward']));
							$totalIn = array_sum($reportData['movements']['daily_total_in']);
							$totalOut = array_sum($reportData['movements']['daily_total_out']);
							?>
							<div class="summary-card">
								<div class="value text-primary"><?php echo $totalProducts; ?></div>
								<div class="label">จำนวนสินค้า LuckGems</div>
							</div>
							<div class="summary-card">
								<div class="value text-success"><?php echo number_format($totalStock, 2); ?></div>
								<div class="label">สต็อกรวม</div>
							</div>
							<div class="summary-card">
								<div class="value text-info"><?php echo number_format($totalIn, 2); ?></div>
								<div class="label">รับเข้ารวม</div>
							</div>
							<div class="summary-card">
								<div class="value text-warning"><?php echo number_format($totalOut, 2); ?></div>
								<div class="label">ส่งออกรวม</div>
							</div>
						</div>
					</div>

					<div class="table-container">
						<div class="table-responsive">
							<table class="table table-stock">
								<tbody>
									<!-- Date Headers -->
									<tr>
										<th class="row-label white-bg" style="min-width:180px">
											<i class="fas fa-calendar me-2"></i>วันที่
											<input type="hidden" name="today" value="<?php echo date('Y-m-d'); ?>">
										</th>
										<?php
										foreach ($reportData['dates'] as $date) {
											$isToday = date('Y-m-d', $date) == date('Y-m-d');
											$bgColor = $this->getDayColorStyle($date);
											$extraClass = '';
											if ($isToday) $extraClass .= ' today';

											$dayNumber = date('w', $date);
											$thaiDayNames = [
												'0' => 'วันอาทิตย์',
												'1' => 'วันจันทร์',
												'2' => 'วันอังคาร',
												'3' => 'วันพุธ',
												'4' => 'วันพฤหัสบดี',
												'5' => 'วันศุกร์',
												'6' => 'วันเสาร์'
											];
											$dayName = $thaiDayNames[$dayNumber];

											echo '<th colspan="' . count($this->productTypeIds) . '" class="date-header' . $extraClass . '" style="background-color: ' . $bgColor . ' !important;">';
											echo '<i class="fas fa-calendar-day me-1"></i>';
											echo $dayName . '<br>' . date("d/m/Y", $date);
											echo '</th>';
										}
										?>
									</tr>

									<!-- Product Headers -->
									<tr>
										<th class="row-label white-bg">
											<i class="fas fa-tag me-2"></i>สินค้า
										</th>
										<?php
										foreach ($reportData['dates'] as $adate) {
											$bgColor = $this->getDayColorStyle($adate);
											foreach ($reportData['products'] as $product) {
												echo '<th class="product-header" style="background-color: ' . $bgColor . ';">';
												echo htmlspecialchars($product['name']);
												echo '</th>';
											}
										}
										?>
									</tr>

									<!-- Balance Forward -->
									<tr class="fade-in">
										<td class="row-label" style="background: white;">
											ยอดยกมา
										</td>
										<?php
										for ($i = 0; $i < count($reportData['dates']); $i++) {
											for ($j = 0; $j < count($this->productTypeIds); $j++) {
												$value = $reportData['movements']['balance_forward'][$j][$i];
												$class = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
												echo "<td class=\"{$class}\">" . number_format($value, 2) . "</td>";
											}
										}
										?>
									</tr>

									<!-- LuckGems In -->
									<tr class="fade-in">
										<td class="row-label" style="background: white; color: #28a745 !important; font-weight: bold;">
											<i class="fas fa-arrow-down me-2"></i>เข้า
										</td>
										<?php
										for ($i = 0; $i < count($reportData['dates']); $i++) {
											for ($j = 0; $j < count($this->productTypeIds); $j++) {
												$value = $reportData['movements']['luckgems_in'][$j][$i];
												$color = $value > 0 ? 'color: #28a745 !important; font-weight: bold;' : 'color: #95a5a6;';
												echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
											}
										}
										?>
									</tr>

									<!-- LuckGems Out -->
									<tr class="fade-in">
										<td class="row-label" style="background: white; color: #dc3545 !important; font-weight: bold;">
											<i class="fas fa-arrow-up me-2"></i>ออก
										</td>
										<?php
										for ($i = 0; $i < count($reportData['dates']); $i++) {
											for ($j = 0; $j < count($this->productTypeIds); $j++) {
												$value = $reportData['movements']['luckgems_out'][$j][$i];
												$color = $value > 0 ? 'color: #dc3545 !important; font-weight: bold;' : 'color: #95a5a6;';
												echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
											}
										}
										?>
									</tr>

									<!-- Total Out -->
									<tr class="fade-in">
										<td class="row-label" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
											ยอดออกรวม
										</td>
										<?php
										for ($i = 0; $i < count($reportData['movements']['daily_total_out']); $i++) {
											$value = $reportData['movements']['daily_total_out'][$i];
											$class = $value > 0 ? 'text-danger font-weight-bold' : 'zero-value';
											echo '<td colspan="' . count($this->productTypeIds) . '" class="' . $class . '">';
											echo '<i class="fas fa-chart-bar me-1"></i>' . number_format($value, 2);
											echo '</td>';
										}
										?>
									</tr>

									<!-- Carry Forward -->
									<tr class="fade-in">
										<td class="row-label" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
											ยอดคงเหลือ
										</td>
										<?php
										for ($i = 0; $i < count($reportData['dates']); $i++) {
											for ($j = 0; $j < count($this->productTypeIds); $j++) {
												$value = $reportData['movements']['carry_forward'][$j][$i];
												if ($value > 0) {
													$color = 'color: #333 !important; font-weight: bold;';
												} elseif ($value < 0) {
													$color = 'color: #e74c3c !important; font-weight: bold;';
												} else {
													$color = 'color: #95a5a6;';
												}
												echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
											}
										}
										?>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
				<script>
					document.addEventListener('DOMContentLoaded', function() {
						const tableLuckGems = document.querySelector('.table-stock');
						if (tableLuckGems) {
							tableLuckGems.style.opacity = '0';
							setTimeout(() => {
								tableLuckGems.style.transition = 'opacity 0.5s ease-in';
								tableLuckGems.style.opacity = '1';
							}, 100);
						}

						const cellsLuckGems = document.querySelectorAll('.luckgems-container .table-stock td, .luckgems-container .table-stock th:not(.product-header)');
						cellsLuckGems.forEach(cell => {
							cell.addEventListener('mouseenter', function() {
								this.style.transform = 'scale(1.05)';
								this.style.zIndex = '10';
								this.style.position = 'relative';
							});

							cell.addEventListener('mouseleave', function() {
								this.style.transform = 'scale(1)';
								this.style.zIndex = 'auto';
								this.style.position = 'static';
							});
						});

						function updateClockLuckGems() {
							const now = new Date();
							const timeString = now.toLocaleString('th-TH');
							const timeSpan = document.querySelector('.luckgems-container .controls-section .text-muted span');
							if (timeSpan) {
								timeSpan.innerHTML = `<i class="fas fa-clock me-2"></i>อัพเดทล่าสุด: ${timeString}`;
							}
						}

						setInterval(updateClockLuckGems, 60000);

						window.exportToCSVLuckGems = function() {
							const table = document.querySelector('.luckgems-container .table-stock');
							const rows = table.querySelectorAll('tr');
							let csv = '';

							rows.forEach(row => {
								const cells = row.querySelectorAll('td, th');
								const rowData = Array.from(cells).map(cell => {
									return '"' + cell.textContent.replace(/"/g, '""') + '"';
								}).join(',');
								csv += rowData + '\n';
							});

							const blob = new Blob(['\ufeff' + csv], {
								type: 'text/csv;charset=utf-8;'
							});
							const link = document.createElement('a');
							const url = URL.createObjectURL(blob);
							link.setAttribute('href', url);
							link.setAttribute('download', 'luckgems_report_' + new Date().toISOString().slice(0, 10) + '.csv');
							link.style.visibility = 'hidden';
							document.body.appendChild(link);
							link.click();
							document.body.removeChild(link);
						};

						function addTooltipsLuckGems() {
							const cells = document.querySelectorAll('.luckgems-container .table-stock td');
							cells.forEach(cell => {
								const value = parseFloat(cell.textContent.replace(/,/g, ''));
								if (!isNaN(value)) {
									let tooltip = '';
									if (value > 0) {
										tooltip = 'มีสต็อก LuckGems: ' + value.toLocaleString('th-TH');
									} else if (value < 0) {
										tooltip = 'ขาดสต็อก LuckGems: ' + Math.abs(value).toLocaleString('th-TH');
									} else {
										tooltip = 'สต็อก LuckGems เท่ากับศูนย์';
									}
									cell.setAttribute('title', tooltip);
								}
							});
						}

						addTooltipsLuckGems();
					});

					document.addEventListener('keydown', function(e) {
						// Only handle Ctrl+E for LuckGems export, avoid conflicts with main stock system
						if ((e.ctrlKey || e.metaKey) && e.key === 'e' && e.target.closest('.luckgems-container')) {
							e.preventDefault();
							if (typeof exportToCSVLuckGems === 'function') {
								exportToCSVLuckGems();
							}
						}
					});
				</script>

				<style media="print">
					body {
						background: white !important;
						-webkit-print-color-adjust: exact;
					}

					.controls-section,
					.summary-cards {
						display: none !important;
					}

					.main-container {
						box-shadow: none !important;
						margin: 0 !important;
						border-radius: 0 !important;
					}

					.table-stock {
						box-shadow: none !important;
						border-radius: 0 !important;
					}

					.table-stock th,
					.table-stock td {
						font-size: 8pt !important;
						padding: 4px !important;
					}

					.hidebyclick {
						display: none !important;
					}
				</style>


			</body>

			</html>

<?php
			return ob_get_clean();
		} catch (Exception $e) {
			return "<div class='alert alert-danger'>Error rendering LuckGems report: " . htmlspecialchars($e->getMessage()) . "</div>";
		}
	}

	public function testConnection()
	{
		try {
			$result = $this->dbc->Query("SELECT COUNT(*) as count FROM bs_products_type");
			if ($result === false) {
				throw new Exception("Test query failed. Check SQL syntax or table existence.");
			}
			$data = $this->dbc->Fetch($result);
			$this->debugLog("Database connection OK. Found " . ($data['count'] ? $data['count'] : 'N/A') . " product types during test.");
			return true;
		} catch (Exception $e) {
			$this->debugLog("Database connection ERROR during test: " . $e->getMessage());
			return false;
		}
	}

	public function verifyLuckGemsData()
	{
		$productIdsString = implode(',', $this->productTypeIds);
		$startDate = date('Y-m-d', $this->reportDisplayStartDate);
		$endDate = date('Y-m-d', $this->reportDisplayEndDate);

		$sql1 = "SELECT 
					product_type,
					DATE(date) as adj_date,
					SUM(amount) as type9_amount
				FROM bs_stock_adjusted_bwd 
				WHERE product_type IN ($productIdsString)
				AND type_id = 9
				AND DATE(date) BETWEEN '$startDate' AND '$endDate'
				GROUP BY product_type, DATE(date)
				ORDER BY adj_date, product_type";

		$type9Data = array();
		$rst1 = $this->dbc->Query($sql1);
		while ($row = $this->dbc->Fetch($rst1)) {
			$type9Data[$row['adj_date']][$row['product_type']] = $row['type9_amount'];
		}

		$sql2 = "SELECT 
					product_type,
					delivery_date,
					SUM(amount) as luckgems_amount
				FROM bs_orders_bwd 
				WHERE product_type IN ($productIdsString)
				AND status > 0
				AND LOWER(TRIM(platform)) = 'luckgems'
				AND delivery_date BETWEEN '$startDate' AND '$endDate'
				GROUP BY product_type, delivery_date
				ORDER BY delivery_date, product_type";

		$luckGemsData = array();
		$rst2 = $this->dbc->Query($sql2);
		while ($row = $this->dbc->Fetch($rst2)) {
			$luckGemsData[$row['delivery_date']][$row['product_type']] = $row['luckgems_amount'];
		}

		for ($time = $this->reportDisplayStartDate; $time <= $this->reportDisplayEndDate; $time += self::SECONDS_IN_DAY) {
			$checkDate = date("Y-m-d", $time);

			foreach ($this->productTypeIds as $productId) {
				$type9Amount = isset($type9Data[$checkDate][$productId]) ? $type9Data[$checkDate][$productId] : 0;
				$luckGemsAmount = isset($luckGemsData[$checkDate][$productId]) ? $luckGemsData[$checkDate][$productId] : 0;

				if ($type9Amount != 0 || $luckGemsAmount != 0) {
					$this->debugLog("LuckGems Data Verification - Date: $checkDate, Product: $productId, In: $type9Amount, Out: $luckGemsAmount");
				}
			}
		}
	}
}

try {
	if (!isset($dbc) || !is_object($dbc) || !method_exists($dbc, 'Query') || !method_exists($dbc, 'Fetch')) {
		throw new Exception("Database connection variable \$dbc not properly initialized.");
	}

	$luckGemsReport = new LuckGemsStockReportManager($dbc);

	if ($luckGemsReport->testConnection()) {
		echo $luckGemsReport->renderReport();
		$luckGemsReport->verifyLuckGemsData();
	} else {
		echo "<div class='alert alert-danger'>Database connection failed. LuckGems report cannot be generated.</div>";
	}
} catch (Exception $e) {
	echo "<div class='alert alert-danger'>FATAL ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

?>