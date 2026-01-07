<?php

class LuckGemsStockReportManager
{
	private $dbc;
	private $reportDisplayStartDate;
	private $reportDisplayEndDate;
	private $productTypeIds;
	private $debug = true;

	const SECONDS_IN_DAY = 86400;
	const DATA_START_DATE = "2025-12-10";
	const DAYS_FORWARD_FROM_DISPLAY_START = 20;

	// ยอดคงเหลือเริ่มต้น ณ วันที่ 10 ธันวาคม 2025
	private $initialStockBalance = [
		1 => 38,
		2 => 29,
		3 => 0,
		4 => 24,
		5 => 0,
		6 => 26,
		7 => 0,
		8 => 23,
		9 => 28,
		10 => 21
	];

	public function __construct($dbc)
	{
		$this->dbc = $dbc;
		$this->productTypeIds = range(1, 10);
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
		$this->reportDisplayStartDate = strtotime("2025-12-10");

		$this->reportDisplayEndDate = strtotime("today");

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
			'front_store_out' => array(),  // เพิ่ม: ขายหน้าร้าน (platform = LuckGems)
			'luckgems_out' => array(),     // ออกปกติ (platform อื่น)
			'pending_pickup_orders' => array()
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
			$this->debugLog("Fetched " . count($data['luckgems_in']) . " distinct LuckGems in date entries from stock_adjusted.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching LuckGems in data from stock_adjusted: " . $e->getMessage());
		}

		try {
			$sqlOrdersIn = "SELECT product_type, delivery_date AS date, SUM(amount) AS total_amount
                        FROM bs_orders_bwd
                        WHERE product_type IN (" . $productIdsString . ")
                        AND status > 0
                        AND orderable_type = 'receive_at_luckgems'
						AND platform != 'LuckGems'
                        AND delivery_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
                        GROUP BY product_type, delivery_date";
			$this->debugLog("Orders In Query: " . $sqlOrdersIn);
			$rst = $this->dbc->Query($sqlOrdersIn);
			$ordersInCount = 0;
			while ($row = $this->dbc->Fetch($rst)) {
				if (!isset($data['luckgems_in'][$row['date']][$row['product_type']])) {
					$data['luckgems_in'][$row['date']][$row['product_type']] = 0;
				}
				$data['luckgems_in'][$row['date']][$row['product_type']] += (float)$row['total_amount'];
				$ordersInCount++;
				$this->debugLog("Orders In Found: Date=" . $row['date'] . ", Product=" . $row['product_type'] . ", Amount=" . $row['total_amount']);
			}
			$this->debugLog("Fetched " . $ordersInCount . " distinct orders in date entries from bs_orders_bwd.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching orders in data: " . $e->getMessage());
		}

		try {
			$sqlFrontStoreOut = "SELECT product_type, accept_date AS date, SUM(amount) AS total_amount
						   FROM bs_orders_bwd
						   WHERE product_type IN (" . $productIdsString . ")
						   AND status > 0
						   AND orderable_type = 'receive_at_luckgems'
						   AND LOWER(TRIM(platform)) = 'luckgems'
						   AND accept_date IS NOT NULL
						   AND accept_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
						   GROUP BY product_type, accept_date";
			$this->debugLog("Front Store Out Query: " . $sqlFrontStoreOut);
			$rst = $this->dbc->Query($sqlFrontStoreOut);
			$frontStoreOutCount = 0;
			while ($row = $this->dbc->Fetch($rst)) {
				$data['front_store_out'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
				$frontStoreOutCount++;
				$this->debugLog("Front Store Out Found: Date=" . $row['date'] . ", Product=" . $row['product_type'] . ", Amount=" . $row['total_amount']);
			}
			$this->debugLog("Fetched " . $frontStoreOutCount . " distinct front store out date entries.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching front store out data: " . $e->getMessage());
		}

		try {
			$sqlLuckGemsOut = "SELECT product_type, accept_date AS date, SUM(amount) AS total_amount
						   FROM bs_orders_bwd
						   WHERE product_type IN (" . $productIdsString . ")
						   AND status > 0
						   AND orderable_type = 'receive_at_luckgems'
						   AND LOWER(TRIM(platform)) != 'luckgems'
						   AND accept_date IS NOT NULL
						   AND accept_date BETWEEN '" . $fullStartDate . "' AND '" . $fullEndDate . "'
						   GROUP BY product_type, accept_date";
			$this->debugLog("LuckGems Out Query: " . $sqlLuckGemsOut);
			$rst = $this->dbc->Query($sqlLuckGemsOut);
			$luckGemsOutCount = 0;
			while ($row = $this->dbc->Fetch($rst)) {
				$data['luckgems_out'][$row['date']][$row['product_type']] = (float)$row['total_amount'];
				$luckGemsOutCount++;
				$this->debugLog("LuckGems Out Found: Date=" . $row['date'] . ", Product=" . $row['product_type'] . ", Amount=" . $row['total_amount']);
			}
			$this->debugLog("Fetched " . $luckGemsOutCount . " distinct LuckGems out date entries.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching LuckGems out data: " . $e->getMessage());
		}

		try {
			$sqlPendingPickup = "SELECT product_type, created, delivery_date, accept_date, amount
							 FROM bs_orders_bwd
							 WHERE product_type IN (" . $productIdsString . ")
							 AND status > 0
							 AND orderable_type = 'receive_at_luckgems'
							 AND delivery_date IS NOT NULL";
			$this->debugLog("Pending Pickup Query: " . $sqlPendingPickup);
			$rst = $this->dbc->Query($sqlPendingPickup);
			$pendingCount = 0;
			while ($row = $this->dbc->Fetch($rst)) {
				$data['pending_pickup_orders'][] = array(
					'product_type' => $row['product_type'],
					'created_date' => $row['created'],
					'delivery_date' => $row['delivery_date'],
					'accept_date' => $row['accept_date'],
					'amount' => (float)$row['amount']
				);
				$pendingCount++;
				$this->debugLog("Pending Pickup Order: Delivery=" . $row['delivery_date'] . ", Accept=" . ($row['accept_date'] ?: 'NULL') . ", Product=" . $row['product_type'] . ", Amount=" . $row['amount']);
			}
			$this->debugLog("Fetched " . $pendingCount . " pending pickup orders.");
		} catch (Exception $e) {
			$this->debugLog("ERROR fetching pending pickup data: " . $e->getMessage());
		}

		return $data;
	}

	private function calculateInitialBalance($allLuckGemsData)
	{
		$this->debugLog("Calculating initial LuckGems balance from bulk data...");
		$balances = array();
		$pendingPickup = array_fill_keys($this->productTypeIds, 0);

		$initialBalanceEndDate = date("Y-m-d", $this->reportDisplayStartDate - self::SECONDS_IN_DAY);
		$this->debugLog("Initial Balance will be calculated up to: " . $initialBalanceEndDate);

		foreach ($this->productTypeIds as $productTypeId) {
			$balance = isset($this->initialStockBalance[$productTypeId]) ? $this->initialStockBalance[$productTypeId] : 0;
			$this->debugLog("Starting balance for product " . $productTypeId . " on " . self::DATA_START_DATE . ": " . number_format($balance, 4));

			$startCalculationDate = strtotime(self::DATA_START_DATE) + self::SECONDS_IN_DAY;
			for ($time = $startCalculationDate; $time <= strtotime($initialBalanceEndDate); $time += self::SECONDS_IN_DAY) {
				$date = date("Y-m-d", $time);

				$dailyIn = isset($allLuckGemsData['luckgems_in'][$date][$productTypeId]) ? $allLuckGemsData['luckgems_in'][$date][$productTypeId] : 0;
				$dailyOut = isset($allLuckGemsData['luckgems_out'][$date][$productTypeId]) ? $allLuckGemsData['luckgems_out'][$date][$productTypeId] : 0;

				$balance += $dailyIn;
				$balance -= $dailyOut;
			}

			$pendingAmount = 0;
			foreach ($allLuckGemsData['pending_pickup_orders'] as $order) {
				if (
					$order['product_type'] == $productTypeId
					&& !empty($order['delivery_date'])
					&& date('Y-m-d', strtotime($order['delivery_date'])) <= $initialBalanceEndDate
					&& (is_null($order['accept_date']) || empty($order['accept_date']) || $order['accept_date'] > $initialBalanceEndDate)  // ← เพิ่ม empty
				) {
					$pendingAmount += $order['amount'];
				}
			}
			$balances[$productTypeId] = $balance;
			$pendingPickup[$productTypeId] = $pendingAmount;

			$this->debugLog("Final Initial LuckGems Balance for product " . $productTypeId . ": " . number_format($balance, 4));
			$this->debugLog("Final Initial Pending Pickup for product " . $productTypeId . ": " . number_format($pendingAmount, 4));
		}

		return array(
			'balance' => $balances,
			'pending_pickup' => $pendingPickup
		);
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
			'pending_pickup_forward' => array_fill(0, count($this->productTypeIds), array()),
			'luckgems_in' => array_fill(0, count($this->productTypeIds), array()),
			'front_store_out' => array_fill(0, count($this->productTypeIds), array()), // เพิ่ม
			'luckgems_out' => array_fill(0, count($this->productTypeIds), array()),
			'total_out' => array_fill(0, count($this->productTypeIds), array()), // รวมออก
			'ready_to_sell' => array_fill(0, count($this->productTypeIds), array()),
			'total_in_stock' => array_fill(0, count($this->productTypeIds), array()),
			'daily_total_balance_forward' => array(),  // สรุปแท่งพร้อมขาย ยกมา
			'daily_total_pending_forward' => array(),  // สรุปรอลูกค้ามารับ ยกมา
			'daily_total_in' => array(),  // สรุปเข้า
			'daily_total_front_store_out' => array(),  // สรุปขายหน้าร้าน
			'daily_total_out' => array(),  // สรุปออก
			'daily_total_total_out' => array(),  // สรุปออกรวม
			'daily_total_ready_to_sell' => array(),  // สรุปแท่งพร้อมขาย
			'daily_total_pending_pickup' => array(),  // สรุปแท่งรอลูกค้ามารับ
			'daily_total_in_stock' => array()  // สรุปรวมของในคลัง
		);

		$luckGemsBalance = $initialBalance['balance'];

		foreach ($dates as $dateIndex => $dateTimestamp) {
			$currentDate = date("Y-m-d", $dateTimestamp);
			$dailyTotalBalanceForward = 0;
			$dailyTotalPendingForward = 0;
			$dailyTotalIn = 0;
			$dailyTotalFrontStoreOut = 0;  // เพิ่ม
			$dailyTotalOut = 0;
			$dailyTotalTotalOut = 0;  // เพิ่ม: รวมออกทั้งหมด
			$dailyTotalReadyToSell = 0;
			$dailyTotalPendingPickup = 0;
			$dailyTotalInStock = 0;

			foreach ($this->productTypeIds as $productIndex => $productTypeId) {
				$data['balance_forward'][$productIndex][] = $luckGemsBalance[$productTypeId];
				$dailyTotalBalanceForward += $luckGemsBalance[$productTypeId];  // เพิ่ม

				$pendingAmount = 0;
				foreach ($allLuckGemsData['pending_pickup_orders'] as $order) {
					if (
						$order['product_type'] == $productTypeId
						&& !empty($order['delivery_date'])  // ← เพิ่ม
						&& date('Y-m-d', strtotime($order['delivery_date'])) <= $currentDate  // ← เปลี่ยน
						&& (is_null($order['accept_date']) || empty($order['accept_date']) || $order['accept_date'] > $currentDate)  // ← เพิ่ม empty
					) {
						$pendingAmount += $order['amount'];
					}
				}
				$data['pending_pickup_forward'][$productIndex][] = $pendingAmount;
				$dailyTotalPendingForward += $pendingAmount;

				$dailyIn = isset($allLuckGemsData['luckgems_in'][$currentDate][$productTypeId]) ? $allLuckGemsData['luckgems_in'][$currentDate][$productTypeId] : 0;
				$data['luckgems_in'][$productIndex][] = $dailyIn;
				$luckGemsBalance[$productTypeId] += $dailyIn;
				$dailyTotalIn += $dailyIn;

				$dailyFrontStoreOut = isset($allLuckGemsData['front_store_out'][$currentDate][$productTypeId]) ? $allLuckGemsData['front_store_out'][$currentDate][$productTypeId] : 0;
				$data['front_store_out'][$productIndex][] = $dailyFrontStoreOut;
				$dailyTotalFrontStoreOut += $dailyFrontStoreOut;

				$dailyOut = isset($allLuckGemsData['luckgems_out'][$currentDate][$productTypeId]) ? $allLuckGemsData['luckgems_out'][$currentDate][$productTypeId] : 0;
				$data['luckgems_out'][$productIndex][] = $dailyOut;
				$dailyTotalOut += $dailyOut;

				$dailyTotalOut_combined = $dailyFrontStoreOut + $dailyOut;
				$data['total_out'][$productIndex][] = $dailyTotalOut_combined;
				$luckGemsBalance[$productTypeId] -= $dailyTotalOut_combined;
				$dailyTotalTotalOut += $dailyTotalOut_combined;

				$readyToSell = $luckGemsBalance[$productTypeId];
				$data['ready_to_sell'][$productIndex][] = $readyToSell;
				$dailyTotalReadyToSell += $readyToSell;

				$totalInStock = $readyToSell + $pendingAmount;
				$data['total_in_stock'][$productIndex][] = $totalInStock;
				$dailyTotalInStock += $totalInStock;

				$dailyTotalPendingPickup += $pendingAmount;
			}

			$data['daily_total_balance_forward'][] = $dailyTotalBalanceForward;
			$data['daily_total_pending_forward'][] = $dailyTotalPendingForward;
			$data['daily_total_in'][] = $dailyTotalIn;
			$data['daily_total_front_store_out'][] = $dailyTotalFrontStoreOut;  // เพิ่ม
			$data['daily_total_out'][] = $dailyTotalOut;
			$data['daily_total_total_out'][] = $dailyTotalTotalOut;
			$data['daily_total_ready_to_sell'][] = $dailyTotalReadyToSell;
			$data['daily_total_pending_pickup'][] = $dailyTotalPendingPickup;
			$data['daily_total_in_stock'][] = $dailyTotalInStock;
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
						position: relative;
					}

					.table-responsive {
						overflow-x: auto;
						position: relative;
					}

					.table-stock {
						border-collapse: separate;
						border-spacing: 0;
						background: white;
						border-radius: 15px;
						overflow: visible;
						box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
						position: relative;
					}

					.table-stock th,
					.table-stock td {
						border: 1px solid #e9ecef;
						padding: 10px 8px;
						text-align: center;
						vertical-align: middle;
						font-size: 0.9rem;
						transition: all 0.3s ease;
						min-width: 75px;
						background-color: white;
					}

					.table-stock th {
						font-weight: 600;
						text-transform: uppercase;
						letter-spacing: 1px;
					}

					.date-header {
						padding: 12px 6px !important;
						font-weight: bold;
						font-size: 0.8rem;
					}

					.product-header {
						color: #2d3436;
						font-weight: bold;
						transition: none !important;
						font-size: 0.8rem;
						line-height: 1.4;
						white-space: normal;
					}

					.row-label {
						background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
						color: white;
						font-weight: bold;
						text-align: left !important;
						padding: 10px 12px !important;
						min-width: 220px;
						position: sticky;
						left: 0;
						z-index: 20;
						box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
					}

					.row-label.white-bg {
						background: white !important;
						color: #333 !important;
						border: 1px solid #e9ecef;
						position: sticky;
						left: 0;
						z-index: 20;
						box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
						text-align: left !important;
						padding: 10px 12px !important;
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
							padding: 6px 4px;
							font-size: 0.8rem;
							min-width: 75px;
						}

						.date-header {
							padding: 8px 4px !important;
						}

						.row-label {
							min-width: 180px;
						}

						.row-label.white-bg {
							min-width: 180px;
						}
					}
				</style>
			</head>

			<body>

				<div class="main-container luckgems-container">
					<div class="controls-section">
						<div class="d-flex justify-content-between align-items-center">
							<h2>
								<div class="text-muted">
									Stock Luck Gems
								</div>
							</h2>
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
							}, $reportData['movements']['total_in_stock']));

							$totalIn = array_sum($reportData['movements']['daily_total_in']);
							$totalOut = array_sum($reportData['movements']['daily_total_total_out']);
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

						<div class="table-container">
							<div class="table-responsive">
								<table class="table table-stock">
									<tbody>
										<tr>
											<th class="row-label white-bg" style="min-width:280px">
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

										<tr>
											<th class="row-label white-bg">
												<i class="fas fa-tag me-2"></i>สินค้า
											</th>
											<?php
											foreach ($reportData['dates'] as $adate) {
												$bgColor = $this->getDayColorStyle($adate);
												foreach ($reportData['products'] as $product) {
													echo '<th class="product-header" style="background-color: ' . $bgColor . ';">';
													// เช็คว่ามี "-" ถ้ามีให้ขึ้นบรรทัดใหม่
													$productName = htmlspecialchars($product['name']);
													$productName = str_replace(' - ', '<br>- ', $productName);
													echo $productName;
													echo '</th>';
												}
											}
											?>
										</tr>

										<tr class="fade-in">
											<td class="row-label" style="background: white;">
												แท่งพร้อมขาย ยกมา
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

										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #ff9800 !important; font-weight: bold;">
												รอลูกค้ามารับ ยกมา
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$value = $reportData['movements']['pending_pickup_forward'][$j][$i];
													$color = $value > 0 ? 'color: #ff9800 !important; font-weight: bold;' : 'color: #95a5a6;';
													echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
												}
											}
											?>
										</tr>


										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #6c63ff !important; font-weight: bold;">
												รวมของในคลังยกมา
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$balanceForward = $reportData['movements']['balance_forward'][$j][$i];
													$pendingForward = $reportData['movements']['pending_pickup_forward'][$j][$i];
													$value = $balanceForward + $pendingForward;

													if ($value > 0) {
														$color = 'color: #6c63ff !important; font-weight: bold;';
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


										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #dc3545 !important; font-weight: bold;">
												<i class="fas fa-arrow-up me-2"></i>ขายหน้าร้าน (แท่งดี)
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$value = $reportData['movements']['front_store_out'][$j][$i];
													$color = $value > 0 ? 'color: #dc3545 !important; font-weight: bold;' : 'color: #95a5a6;';
													echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
												}
											}
											?>
										</tr>


										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #dc3545 !important; font-weight: bold;">
												<i class="fas fa-arrow-up me-2"></i>ลูกค้ามารับแท่ง
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

										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #e53935 !important; font-weight: bold;">
												รวมออก
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$value = $reportData['movements']['total_out'][$j][$i];
													$color = $value > 0 ? 'color: #e53935 !important; font-weight: bold;' : 'color: #95a5a6;';
													echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
												}
											}
											?>
										</tr>


										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #333 !important; font-weight: bold;">
												แท่งพร้อมขาย
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$value = $reportData['movements']['ready_to_sell'][$j][$i];
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
										<tr class="fade-in">
											<td class="row-label" style="background: white; color: #ff9800 !important; font-weight: bold;">
												แท่งรอลูกค้ามารับ
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$value = $reportData['movements']['pending_pickup_forward'][$j][$i];
													$color = $value > 0 ? 'color: #ff9800 !important; font-weight: bold;' : 'color: #95a5a6;';
													echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
												}
											}
											?>
										</tr>


										<tr class="fade-in">
											<td class="row-label" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
												รวมของในคลัง
											</td>
											<?php
											for ($i = 0; $i < count($reportData['dates']); $i++) {
												for ($j = 0; $j < count($this->productTypeIds); $j++) {
													$readyToSell = $reportData['movements']['ready_to_sell'][$j][$i];
													$pendingPickup = $reportData['movements']['pending_pickup_forward'][$j][$i];
													$value = $readyToSell + $pendingPickup;

													if ($value > 0) {
														$color = 'color: #667eea !important; font-weight: bold;';
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
					<script>
						(function() {

							function attemptScroll() {
								const todayColumn = document.querySelector('.luckgems-container .date-header.today');
								const rowLabel = document.querySelector('.luckgems-container .row-label.white-bg');
								const scrollContainer = document.querySelector('.luckgems-container .table-responsive'); 

								console.log('[LuckGems] todayColumn:', todayColumn);
								console.log('[LuckGems] rowLabel:', rowLabel);
								console.log('[LuckGems] scrollContainer:', scrollContainer);

								if (todayColumn && scrollContainer) {
									const rowLabelWidth = rowLabel ? rowLabel.offsetWidth : 280;
									const todayOffset = todayColumn.offsetLeft;
									const scrollPosition = todayOffset - rowLabelWidth - 100;


									scrollContainer.scrollLeft = Math.max(0, scrollPosition);


									setTimeout(() => {
										todayColumn.style.transition = 'all 0.5s ease';
										todayColumn.style.transform = 'scale(1.05)';
										todayColumn.style.boxShadow = '0 0 25px rgba(0,123,255,0.8)';

										setTimeout(() => {
											todayColumn.style.transform = 'scale(1)';
											todayColumn.style.boxShadow = '';
										}, 800);
									}, 300);

									return true;
								} else {
									return false;
								}
							}

							if (document.readyState === 'complete') {
								setTimeout(attemptScroll, 500);
							} else {
								window.addEventListener('load', function() {
									setTimeout(attemptScroll, 500);
								});
							}
						})();
					</script>
			</body>

			</html><?php
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
					accept_date,
					SUM(amount) as luckgems_amount
				FROM bs_orders_bwd 
				WHERE product_type IN ($productIdsString)
				AND status > 0
				AND LOWER(TRIM(platform)) = 'luckgems'
				AND accept_date BETWEEN '$startDate' AND '$endDate'
				GROUP BY product_type, accept_date
				ORDER BY accept_date, product_type";

				$luckGemsData = array();
				$rst2 = $this->dbc->Query($sql2);
				while ($row = $this->dbc->Fetch($rst2)) {
					$luckGemsData[$row['accept_date']][$row['product_type']] = $row['luckgems_amount'];
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