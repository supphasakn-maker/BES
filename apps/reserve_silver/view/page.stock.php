<style>
	.stock-forecast-table {
		font-size: 0.85rem;
		border-collapse: separate !important;
		border-spacing: 0;
	}

	.stock-forecast-table td,
	.stock-forecast-table th {
		border: 1px solid #dee2e6;
		padding: 8px 6px;
		vertical-align: middle;
		position: relative;
	}

	/* Sticky left column */
	.sticky-left {
		position: sticky;
		left: 0;
		z-index: 10;
		min-width: 150px;
		text-align: center;

	}

	/* Header cells */
	.header-cell {
		background-color: #6c757d;
		color: white;
		font-weight: bold;
		text-align: center;
		border-color: #5a6268 !important;
	}

	/* Date headers */
	.date-header {
		padding: 12px 8px;
		font-weight: bold;
		border: 1px solid #dee2e6 !important;
	}

	.date-content {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.day-name {
		font-size: 0.9rem;
		font-weight: bold;
		margin-bottom: 2px;
	}

	.date-number {
		font-size: 0.8rem;
		color: #6c757d;
	}

	/* Product headers */
	.product-name {
		font-size: 0.75rem;
		font-weight: 600;
		text-align: center;
		padding: 6px 4px;
		border-color: #dee2e6 !important;
		max-width: 80px;
		word-wrap: break-word;
	}

	.product-content {
		line-height: 1.2;
		color: #495057;
	}

	/* Row headers */
	.row-header {
		font-weight: bold;
		text-align: center;
		font-size: 0.9rem;
		border-right: 1px solid #dee2e6 !important;
	}

	/* Data cells */
	.data-cell {
		background-color: #ffffff;
		transition: all 0.2s ease;
		border-color: #dee2e6 !important;
	}

	.data-cell:hover {
		background-color: #f8f9fa;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	}

	.data-number {
		font-weight: 500;
		color: #495057;
	}

	/* Value highlighting */
	.positive-value {
		background-color: #d4edda !important;
		color: #155724;
	}

	.negative-value {
		background-color: #f8d7da !important;
		color: #721c24;
		font-weight: bold;
	}

	.low-value {
		background-color: #fff3cd !important;
		color: #856404;
	}

	/* Data rows - subtle backgrounds */
	.balance-forward .data-cell {
		background-color: #f8f9fa;
	}

	.stock-in .data-cell {
		background-color: #ffffff;
	}

	.closing-balance .data-cell {
		background-color: #ffffff;
	}

	/* Cards */
	.card {
		border-radius: 8px;
		overflow: hidden;
	}

	.card.shadow-sm {
		box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.stock-forecast-table {
			font-size: 0.7rem;
		}

		.data-cell,
		.product-name {
			padding: 4px 2px;
		}

		.date-content {
			font-size: 0.7rem;
		}

		.day-name {
			font-size: 0.75rem;
		}

		.date-number {
			font-size: 0.65rem;
		}
	}

	/* Table scroll styling */
	.table-responsive::-webkit-scrollbar {
		height: 6px;
	}

	.table-responsive::-webkit-scrollbar-track {
		background: #f1f1f1;
	}

	.table-responsive::-webkit-scrollbar-thumb {
		background: #c1c1c1;
		border-radius: 3px;
	}

	.table-responsive::-webkit-scrollbar-thumb:hover {
		background: #a8a8a8;
	}
</style><?php
		global $dbc;
		$start_date = strtotime("2023-08-05");
		$today = time();
		$totaldate = floor((time() - $today) / 86400) + 7;

		$aDate = array();
		$PID = array(1, 3, 4, 5, 6, 8, 7, 9, 10, 16, 11, 12, 13, 14, 15, 16, 17, 18, 19, 24, 25, 20, 21, 26, 22, 23,27,28);
		$aBalance = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0);
		$aTotal = array();
		$aProduct = array();

		for ($i = 0; $i < count($PID); $i++) {
			$product = $dbc->GetRecord("bs_products_import", "*", "code=" . $PID[$i]);
			array_push($aProduct, $product);

			$line_production = $dbc->GetRecord(
				"bs_reserve_silver",
				"SUM(weight_lock)",
				"lock_date BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $today - 86400) . "' AND type='2' AND brand = " . $PID[$i]
			);

			$bs_incoming_plans = $dbc->GetRecord(
				"bs_incoming_plans",
				"SUM(amount)",
				"import_date BETWEEN '" . date("Y-m-d", $start_date - 86400) . "' AND '" . date("Y-m-d", $today - 86400) . "' AND remark = " . $PID[$i]
			);

			// ใช้การคำนวณแบบเดิม
			$aBalance[$i] += $line_production[0] ?? 0;
		}

		$aaData_BF = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
		$aaData_In = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
		$aaData_SB = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
		$aaData_MR = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
		$aaData_Out = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
		$aaData_CF = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array(), array());

		for ($i = 0; $i <= $totaldate; $i++) {
			$iDate = $today + $i * 86400;
			array_push($aDate, $iDate);

			$totalToday = 0;
			for ($j = 0; $j < count($PID); $j++) {
				array_push($aaData_BF[$j], $aBalance[$j]);

				$line = $dbc->GetRecord("bs_reserve_silver", "SUM(weight_lock)", "lock_date LIKE '" . date("Y-m-d", $iDate) . "' AND type='2' AND brand = " . $PID[$j]);
				array_push($aaData_MR[$j], $line[0]);
				$aBalance[$j] += $line[0]; // กลับไปใช้การคำนวณแบบเดิม

				$line = $dbc->GetRecord("bs_incoming_plans", "SUM(amount)", "import_date LIKE '" . date("Y-m-d", $iDate) . "' AND remark = " . $PID[$j]);
				array_push($aaData_Out[$j], $line[0]);
				$aBalance[$j] -= $line[0]; // กลับไปใช้การคำนวณแบบเดิม

				$totalToday += $line[0];

				array_push($aaData_CF[$j], $aBalance[$j]);
			}
			array_push($aTotal, $totalToday);
		}
		?>

<div class="container-fluid px-0">
	<div class="row mb-3">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>

					<p class="text-muted mb-0">ช่วงวันที่: <?php echo date('d/m/Y', $aDate[0]); ?> - <?php echo date('d/m/Y', end($aDate)); ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="card shadow-sm border-0">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-bordered table-sm mb-0 stock-forecast-table">
					<tbody>
						<!-- Header วันที่ -->
						<tr class="header-dates">
							<th class="sticky-left header-cell">
								<div class="d-flex align-items-center justify-content-center">
									วันที่
								</div>
							</th>
							<?php
							foreach ($aDate as $date) {
								$day_name = date("l", $date);
								$class = "";

								switch ($day_name) {
									case "Monday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Tuesday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Wednesday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Thursday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Friday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Saturday":
										$class = "background-color: #e9ecef; border: 1px solid #dee2e6;";
										break;
									case "Sunday":
										$class = "background-color: #f8d7da; border: 1px solid #f5c6cb;";
										break;
								}

								echo '<td colspan="28" class="text-center date-header" style="' . $class . ' color: #495057;">';
								echo '<div class="date-content">';
								echo '<div class="day-name">' . $day_name . '</div>';
								echo '<div class="date-number">' . date("d/m/Y", $date) . '</div>';
								echo '</div>';
								echo '</td>';
							}
							?>
						</tr>

						<!-- Header สินค้า -->
						<tr class="header-products">
							<td class="sticky-left header-cell">
								<div class="d-flex align-items-center justify-content-center">
									สินค้า
								</div>
							</td>
							<?php
							foreach ($aDate as $adate) {
								$day_name = date("l", $adate);
								$class = "";

								switch ($day_name) {
									case "Monday":
									case "Tuesday":
									case "Wednesday":
									case "Thursday":
									case "Friday":
										$class = "background-color: #ffffff; border: 1px solid #dee2e6;";
										break;
									case "Saturday":
										$class = "background-color: #f8f9fa; border: 1px solid #dee2e6;";
										break;
									case "Sunday":
										$class = "background-color: #fff5f5; border: 1px solid #fed7d7;";
										break;
								}

								foreach ($aProduct as $product) {
									echo '<td class="text-center product-name" style="' . $class . '">';
									echo '<div class="product-content">';
									echo htmlspecialchars($product['name']);
									echo '</div>';
									echo '</td>';
								}
							}
							?>
						</tr>

						<!-- ยอดยกมา -->
						<tr class="data-row balance-forward">
							<td class="sticky-left row-header bg-info text-white">
								ยอดยกมา
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									echo '<td class="text-end data-cell">';
									echo '<span class="data-number font-weight-bold">' . number_format($aaData_BF[$j][$i], 4) . '</span>';
									echo '</td>';
								}
							}
							?>
						</tr>

						<!-- สินค้าเข้า -->
						<tr class="data-row stock-in">
							<td class="sticky-left row-header bg-success text-white">
								<i class="fas fa-arrow-down me-2"></i>สินค้าเข้า
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_Out[$j][$i];
									$highlight = $value > 0 ? 'positive-value' : '';
									echo '<td class="text-end data-cell ' . $highlight . '">';
									echo '<span class="data-number">' . number_format($value, 4) . '</span>';
									echo '</td>';
								}
							}
							?>
						</tr>

						<!-- คงเหลือ -->
						<tr class="data-row closing-balance">
							<td class="sticky-left row-header bg-primary text-white">
								คงเหลือ
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_CF[$j][$i];
									$cellClass = '';
									if ($value < 0) {
										$cellClass = 'negative-value';
									} elseif ($value < 100) {
										$cellClass = 'low-value';
									}
									echo '<td class="text-end data-cell ' . $cellClass . '">';
									echo '<span class="data-number font-weight-bold text-danger">' . number_format($value, 4) . '</span>';
									echo '</td>';
								}
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- สรุปข้อมูล -->
	<div class="row mt-4">
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body text-center">
					<div class="text-primary mb-2">
						<i class="fas fa-chart-pie fa-2x"></i>
					</div>
					<h6 class="card-title">สินค้าทั้งหมด</h6>
					<h4 class="text-primary"><?php echo count($PID); ?> รายการ</h4>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body text-center">
					<div class="text-warning mb-2">
						<i class="fas fa-exclamation-triangle fa-2x"></i>
					</div>
					<h6 class="card-title">สินค้าเสี่ยงขาดแคลน</h6>
					<?php
					$risk_count = 0;
					foreach ($PID as $j => $pid) {
						$min_value = min($aaData_CF[$j]);
						if ($min_value < 100) $risk_count++;
					}
					?>
					<h4 class="text-warning"><?php echo $risk_count; ?> รายการ</h4>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body text-center">
					<div class="text-success mb-2">
						<i class="fas fa-calendar-check fa-2x"></i>
					</div>
					<h6 class="card-title">ช่วงเวลาคาดการณ์</h6>
					<h4 class="text-success"><?php echo $totaldate + 1; ?> วัน</h4>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.markdown-result table.table-stock td,
	.markdown-result table th,
	.table-bordered td,
	.table-bordered th {
		border: 1px solid #dee2e6;
	}

	.table-stock {
		background-color: white;
	}
</style>