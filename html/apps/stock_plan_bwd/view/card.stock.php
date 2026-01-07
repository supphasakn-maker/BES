<?php

global $dbc;

$start_date = strtotime("2024-12-31");
$today = strtotime("-10 day");
$totaldate = 15;

function executeQuery($dbc, $sql)
{
	try {
		$rst = $dbc->Query($sql);
		if (!$rst) {
			error_log("Database query failed");
			return false;
		}
		return $rst;
	} catch (Exception $e) {
		error_log("Query execution error: " . $e->getMessage());
		return false;
	}
}

function fetchResult($dbc, $rst)
{
	if (!$rst) return null;
	$result = $dbc->Fetch($rst);
	return $result ? $result : null;
}

$aDate = array();
$PID = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
$aBalance = array(99.0000, 93.0000, 46.0000, 15.0000, 27, 21.0000, 18.0000, 20.0000, -26.0000, -23.0000, 37.0000, 20.0000);

$aBalanceWithoutAdjustment = $aBalance;

$aTotal = array();
$aTotalDelivery = array();
$aProduct = array();

for ($i = 0; $i < count($PID); $i++) {
	$product = $dbc->GetRecord("bs_products_type", "*", "id=" . intval($PID[$i]));
	if ($product) {
		array_push($aProduct, $product);
	} else {
		array_push($aProduct, ['id' => $PID[$i], 'name' => 'Unknown Product']);
	}
}

for ($i = 0; $i < count($PID); $i++) {
	$productId = intval($PID[$i]);
	$startDateStr = date("Y-m-d", $start_date - 86400);
	$todayStr = date("Y-m-d", $today - 86400);

	$line_production = $dbc->GetRecord(
		"bs_stock_bwd",
		"SUM(amount)",
		"submited BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_type = {$productId}"
	);
	$production_amount = isset($line_production[0]) ? floatval($line_production[0]) : 0;
	$aBalance[$i] += $production_amount;
	$aBalanceWithoutAdjustment[$i] += $production_amount;

	$line_order = $dbc->GetRecord(
		"bs_orders_bwd",
		"SUM(amount)",
		"DATE(created) BETWEEN '{$startDateStr}' AND '{$todayStr}' AND status > 0 AND product_type = {$productId} AND platform != 'LuckGems'"
	);
	$order_amount = isset($line_order[0]) ? floatval($line_order[0]) : 0;
	$aBalance[$i] -= $order_amount;
	$aBalanceWithoutAdjustment[$i] -= $order_amount;

	$sql = "SELECT id FROM bs_stock_adjust_type_bwd WHERE type = 1 ORDER BY sort_id";
	$rst_deposit_types = executeQuery($dbc, $sql);
	if ($rst_deposit_types) {
		while ($type_item = $dbc->Fetch($rst_deposit_types)) {
			$typeId = intval($type_item['id']);
			$line = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "date BETWEEN '{$startDateStr}' AND '{$todayStr}' AND product_type = {$productId} AND type_id = {$typeId}");
			$daily_adjusted_amount = isset($line[0]) ? floatval($line[0]) : 0;

			if ($typeId == 9) {
				$aBalance[$i] -= $daily_adjusted_amount;
				$aBalanceWithoutAdjustment[$i] -= $daily_adjusted_amount;
			} else {
				$aBalance[$i] += $daily_adjusted_amount;
			}
		}
	}
}

$aaData_BF = array_fill(0, count($PID), array());
$aaData_In = array_fill(0, count($PID), array());
$aaData_Out = array_fill(0, count($PID), array());
$aaData_Delivery = array_fill(0, count($PID), array());
$aaData_CF = array_fill(0, count($PID), array());

$aaaDataDeposit = array();
$sql = "SELECT id, name FROM bs_stock_adjust_type_bwd WHERE type = 1 AND id != 9 ORDER BY sort_id";
$rst = executeQuery($dbc, $sql);
if ($rst) {
	while ($item = $dbc->Fetch($rst)) {
		array_push($aaaDataDeposit, array(
			array("id" => intval($item['id']), "name" => htmlspecialchars($item['name'])),
			...array_fill(0, count($PID), array())
		));
	}
}

$aaData_Total = array_fill(0, count($PID), array());

for ($i = 0; $i <= $totaldate; $i++) {
	$iDate = $today + $i * 86400;
	array_push($aDate, $iDate);

	$totalToday = 0;
	$totalDeliveryToday = 0;
	$currentDateStr = date("Y-m-d", $iDate);

	for ($j = 0; $j < count($PID); $j++) {
		$productId = intval($PID[$j]);

		array_push($aaData_BF[$j], $aBalanceWithoutAdjustment[$j]);

		$line = $dbc->GetRecord("bs_stock_bwd", "SUM(amount)", "submited LIKE '{$currentDateStr}' AND product_type = {$productId}");
		$stock_in = isset($line[0]) ? floatval($line[0]) : 0;
		array_push($aaData_In[$j], $stock_in);
		$aBalance[$j] += $stock_in;
		$aBalanceWithoutAdjustment[$j] += $stock_in;

		// ออก (created) 
		$line = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "DATE(created) = '{$currentDateStr}' AND status > 0 AND product_type = {$productId} AND platform != 'LuckGems'");
		$orders_out = isset($line[0]) ? floatval($line[0]) : 0;
		array_push($aaData_Out[$j], $orders_out);
		$aBalance[$j] -= $orders_out;
		$aBalanceWithoutAdjustment[$j] -= $orders_out;
		$totalToday += $orders_out;

		// เพิ่มข้อมูล delivery_date 
		$line_delivery = $dbc->GetRecord("bs_orders_bwd", "SUM(amount)", "DATE(delivery_date) = '{$currentDateStr}' AND status > 0 AND product_type = {$productId} AND platform != 'LuckGems'");
		$delivery_amount = isset($line_delivery[0]) ? floatval($line_delivery[0]) : 0;
		array_push($aaData_Delivery[$j], $delivery_amount);
		$totalDeliveryToday += $delivery_amount;

		foreach ($aaaDataDeposit as $k => $deposit) {
			$typeId = intval($deposit[0]['id']);

			$line = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "date <= '{$currentDateStr}' AND product_type = {$productId} AND type_id = {$typeId}");
			$cumulative_amount = isset($line[0]) ? floatval($line[0]) : 0;
			array_push($aaaDataDeposit[$k][$j + 1], $cumulative_amount);

			$line = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "date = '{$currentDateStr}' AND product_type = {$productId} AND type_id = {$typeId}");
			$daily_amount = isset($line[0]) ? floatval($line[0]) : 0;
			$aBalance[$j] += $daily_amount;
		}

		$line = $dbc->GetRecord("bs_stock_adjusted_bwd", "SUM(amount)", "date = '{$currentDateStr}' AND product_type = {$productId} AND type_id = 9");
		$daily_type9_amount = isset($line[0]) ? floatval($line[0]) : 0;
		if ($daily_type9_amount != 0) {
			$aBalance[$j] -= $daily_type9_amount;
			$aBalanceWithoutAdjustment[$j] -= $daily_type9_amount;
		}

		array_push($aaData_CF[$j], $aBalanceWithoutAdjustment[$j]);
		array_push($aaData_Total[$j], $aBalanceWithoutAdjustment[$j]);
	}

	array_push($aTotal, $totalToday);
	array_push($aTotalDelivery, $totalDeliveryToday);
}

function renderTableCell($value, $class = '', $align = 'center', $isNegative = false)
{
	$classAttr = $class ? "class=\"{$class}\"" : '';
	$negativeClass = $isNegative && $value < 0 ? 'text-danger font-weight-bold' : '';
	$formattedValue = is_numeric($value) ? number_format($value, 2) : $value;

	return "<td align=\"{$align}\" {$classAttr} class=\"{$negativeClass}\">" . $formattedValue . "</td>";
}

function getDayColorStyle($date)
{
	$dayNumber = date("w", $date);
	$dayColors = [
		'0' => '#ffebee',
		'1' => '#fffde7',
		'2' => '#fce4ec',
		'3' => '#e8f5e8',
		'4' => '#fff3e0',
		'5' => '#e1f5fe',
		'6' => '#f3e5f5'
	];

	return isset($dayColors[$dayNumber]) ? $dayColors[$dayNumber] : '#ffffff';
}

function getRowTypeStyle($type)
{
	$styles = [
		'header' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;',
		'balance' => 'background: white;',
		'in' => 'background: white;',
		'out' => 'background: white;',
		'delivery' => 'background: white;',
		'total_in' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;',
		'total_out' => 'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;',
		'total_delivery' => 'background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #333;',
		'final' => 'background: linear-gradient(135deg, #1e7e34 0%, #155724 100%); color: white;'
	];

	return isset($styles[$type]) ? $styles[$type] : '';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		body {
			background: white;
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

		.toggle-btn {
			background: linear-gradient(135deg, #e0e7ee 0%, #f0f4f8 100%);
			border: none;
			color: #555;
			padding: 10px 20px;
			border-radius: 25px;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(224, 231, 238, 0.4);
		}

		.toggle-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(224, 231, 238, 0.6);
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

		.in-text-green {
			color: #28a745 !important;
			font-weight: bold;
		}

		.out-text-red {
			color: #dc3545 !important;
			font-weight: bold;
		}

		.delivery-text-blue {
			color: #007bff !important;
			font-weight: bold;
		}

		.dark-green-value {
			color: #155724 !important;
			font-weight: bold;
		}

		.loading-spinner {
			display: inline-block;
			width: 20px;
			height: 20px;
			border: 3px solid rgba(255, 255, 255, .3);
			border-radius: 50%;
			border-top-color: #fff;
			animation: spin 1s ease-in-out infinite;
		}

		@keyframes spin {
			to {
				transform: rotate(360deg);
			}
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

		.weekend {
			background-color: inherit !important;
		}

		.today {
			border: 2px solid #007bff;
		}

		.table-stock th,
		.table-stock td {
			padding: 15px 10px;
			font-size: 1rem;
		}

		.date-header {
			padding: 25px 10px !important;
			font-size: 1rem;
		}

		.product-header {
			font-size: 0.9rem;
			padding: 15px 8px;
		}

		@media (max-width: 768px) {

			.table-stock th,
			.table-stock td {
				padding: 10px 6px;
				font-size: 0.85rem;
			}

			.date-header {
				padding: 15px 6px !important;
			}
		}
	</style>
</head>

<body>

	<div class="main-container" style="display: none;">
		<div class="controls-section">
			<div class="d-flex justify-content-between align-items-center">
				<div class="text-muted">
					<i class="fas fa-chart-line me-2"></i>Stock Report
				</div>
				<div class="text-muted">
					<i class="fas fa-calendar-alt me-2"></i>
					ช่วงวันที่: <?php echo date("d/m/Y", $aDate[0]) . " - " . date("d/m/Y", end($aDate)); ?>
					<span class="ms-3">
						<i class="fas fa-clock me-2"></i>
						อัพเดทล่าสุด: <?php echo date("d/m/Y H:i:s"); ?>
					</span>
				</div>
			</div>

			<div class="summary-cards mt-3" id="summaryCards">
				<?php
				$totalProducts = count($PID);
				$totalStock = array_sum(array_map(function ($arr) {
					return end($arr);
				}, $aaData_Total));
				$totalIn = array_sum(array_map('array_sum', $aaData_In));
				$totalOut = array_sum(array_map('array_sum', $aaData_Out));
				$totalDelivery = array_sum(array_map('array_sum', $aaData_Delivery));
				?>
				<div class="summary-card">
					<div class="value text-primary"><?php echo $totalProducts; ?></div>
					<div class="label">จำนวนสินค้า</div>
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
					<div class="label">ส่งออกรวม (Created)</div>
				</div>
				<div class="summary-card">
					<div class="value text-primary"><?php echo number_format($totalDelivery, 2); ?></div>
					<div class="label">ส่งรวม (Delivery)</div>
				</div>
			</div>
		</div>
		<button class="btn toggle-btn" onclick="toggleDetails()">
			<i class="fas fa-eye me-2"></i> Show / Hide Details
		</button>
		<div class="table-container">
			<div class="table-responsive">
				<table class="table table-stock">
					<tbody>
						<tr>
							<th class="row-label white-bg" style="min-width:180px">
								<i class="fas fa-calendar me-2"></i>วันที่
								<input type="hidden" name="today" value="<?php echo date('Y-m-d'); ?>">
							</th>
							<?php
							foreach ($aDate as $date) {
								$isToday = date('Y-m-d', $date) == date('Y-m-d');
								$bgColor = getDayColorStyle($date);
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

								echo '<th colspan="' . count($PID) . '" class="date-header' . $extraClass . '" style="background-color: ' . $bgColor . ' !important;">';
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
							foreach ($aDate as $adate) {
								$bgColor = getDayColorStyle($adate);
								foreach ($aProduct as $product) {
									echo '<th class="product-header" style="background-color: ' . $bgColor . ';">';
									echo htmlspecialchars($product['name']);
									echo '</th>';
								}
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="<?php echo getRowTypeStyle('balance'); ?>">
								ยอดยกมา
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_BF[$j][$i];
									$class = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
									echo "<td class=\"{$class}\">" . number_format($value, 2) . "</td>";
								}
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="background: white; color: #28a745 !important; font-weight: bold;">
								<i class="fas fa-arrow-down me-2"></i>เข้า
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_In[$j][$i];
									$color = $value > 0 ? 'color: #28a745 !important; font-weight: bold;' : 'color: #95a5a6;';
									echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
								}
							}
							?>
						</tr>

						<?php
						foreach ($aaaDataDeposit as $k => $deposit) {
							echo '<tr class="fade-in hidebyclick" style="display: none;">';
							echo '<td class="row-label" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); color: white;">';
							echo '<i class="fas fa-plus-circle me-2"></i>' . htmlspecialchars($deposit[0]['name']);
							echo '</td>';
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $deposit[$j + 1][$i];
									$class = $value > 0 ? 'positive-value' : ($value < 0 ? 'negative-value' : 'zero-value');
									echo "<td class=\"{$class}\">" . number_format($value, 2) . "</td>";
								}
							}
							echo '</tr>';
						}
						?>

						<tr class="fade-in">
							<td class="row-label" style="background: white; color: #dc3545 !important; font-weight: bold;">
								<i class="fas fa-arrow-up me-2"></i>สั่งซื้อ
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_Out[$j][$i];
									$color = $value > 0 ? 'color: #dc3545 !important; font-weight: bold;' : 'color: #95a5a6;';
									echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
								}
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="background: white; color: #007bff !important; font-weight: bold;">
								<i class="fas fa-truck me-2"></i>ส่ง
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_Delivery[$j][$i];
									$color = $value > 0 ? 'color: #007bff !important; font-weight: bold;' : 'color: #95a5a6;';
									echo "<td style=\"{$color}\">" . number_format($value, 2) . "</td>";
								}
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="<?php echo getRowTypeStyle('total_out'); ?>">
								ยอดสั่งซื้อรวม
							</td>
							<?php
							for ($i = 0; $i < count($aTotal); $i++) {
								$value = $aTotal[$i];
								$class = $value > 0 ? 'text-danger font-weight-bold' : 'zero-value';
								echo '<td colspan="' . count($PID) . '" class="' . $class . '">';
								echo '<i class="fas fa-chart-bar me-1"></i>' . number_format($value, 2);
								echo '</td>';
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="<?php echo getRowTypeStyle('total_delivery'); ?>">
								ยอดส่งรวม
							</td>
							<?php
							for ($i = 0; $i < count($aTotalDelivery); $i++) {
								$value = $aTotalDelivery[$i];
								$class = $value > 0 ? 'text-primary font-weight-bold' : 'zero-value';
								echo '<td colspan="' . count($PID) . '" class="' . $class . '">';
								echo '<i class="fas fa-truck me-1"></i>' . number_format($value, 2);
								echo '</td>';
							}
							?>
						</tr>

						<tr class="fade-in">
							<td class="row-label" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
								ยอดคงเหลือยกไป
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_CF[$j][$i];
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
							<td class="row-label" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;" title="Available Stock">
								สินค้าคงคลัง
							</td>
							<?php
							for ($i = 0; $i < count($aDate); $i++) {
								for ($j = 0; $j < count($PID); $j++) {
									$value = $aaData_Total[$j][$i];
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
		function toggleDetails() {
			const elements = document.querySelectorAll('.hidebyclick');
			const btn = document.querySelector('.toggle-btn');
			const icon = btn.querySelector('i');

			elements.forEach(el => {
				if (el.style.display === 'none') {
					el.style.display = '';
					el.classList.add('fade-in');
				} else {
					el.style.display = 'none';
				}
			});

			if (elements[0] && elements[0].style.display === 'none') {
				icon.className = 'fas fa-eye me-2';
				btn.innerHTML = '<i class="fas fa-eye me-2"></i>แสดงรายละเอียด';
			} else {
				icon.className = 'fas fa-eye-slash me-2';
				btn.innerHTML = '<i class="fas fa-eye-slash me-2"></i>ซ่อนรายละเอียด';
			}
		}

		document.addEventListener('DOMContentLoaded', function() {
			const table = document.querySelector('.table-stock');
			if (table) {
				table.style.opacity = '0';
				setTimeout(() => {
					table.style.transition = 'opacity 0.5s ease-in';
					table.style.opacity = '1';
				}, 100);
			}

			const cells = document.querySelectorAll('.table-stock td, .table-stock th:not(.product-header)');
			cells.forEach(cell => {
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

			let refreshInterval;

			function startAutoRefresh(minutes = 5) {
				refreshInterval = setInterval(() => {
					if (confirm('ต้องการรีเฟรชข้อมูลหรือไม่?')) {
						location.reload();
					}
				}, minutes * 60 * 1000);
			}

			window.exportToCSV = function() {
				const table = document.querySelector('.table-stock');
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
				link.setAttribute('download', 'stock_report_2days_' + new Date().toISOString().slice(0, 10) + '.csv');
				link.style.visibility = 'hidden';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			};

			window.printReport = function() {
				window.print();
			};

			function updateClock() {
				const now = new Date();
				const timeString = now.toLocaleString('th-TH');
				const timeSpan = document.querySelector('.controls-section .text-muted span');
				if (timeSpan) {
					timeSpan.innerHTML = `<i class="fas fa-clock me-2"></i>อัพเดทล่าสุด: ${timeString}`;
				}
			}

			setInterval(updateClock, 60000);
		});

		document.addEventListener('keydown', function(e) {
			if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
				e.preventDefault();
				toggleDetails();
			}

			if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
				e.preventDefault();
				if (typeof exportToCSV === 'function') {
					exportToCSV();
				}
			}
		});

		function addTooltips() {
			const cells = document.querySelectorAll('.table-stock td');
			cells.forEach(cell => {
				const value = parseFloat(cell.textContent.replace(/,/g, ''));
				if (!isNaN(value)) {
					let tooltip = '';
					if (value > 0) {
						tooltip = 'มีสต็อก: ' + value.toLocaleString('th-TH');
					} else if (value < 0) {
						tooltip = 'ขาดสต็อก: ' + Math.abs(value).toLocaleString('th-TH');
					} else {
						tooltip = 'สต็อกเท่ากับศูนย์';
					}
					cell.setAttribute('title', tooltip);
				}
			});
		}

		addTooltips();
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

		@media (max-width: 576px) {
			.main-container {
				margin: 10px;
				border-radius: 10px;
			}

			.table-container {
				padding: 15px;
			}

			.summary-cards {
				grid-template-columns: repeat(2, 1fr);
			}

			.summary-card {
				padding: 15px;
			}

			.summary-card .value {
				font-size: 1.2rem;
			}

			.controls-section {
				padding: 15px;
			}

			.toggle-btn {
				padding: 8px 16px;
				font-size: 0.9rem;
			}
		}

		@media (prefers-color-scheme: dark) {
			.main-container {
				background: rgba(33, 37, 41, 0.95);
				color: #ffffff;
			}

			.controls-section {
				background: #495057;
				border-color: #6c757d;
			}

			.summary-card {
				background: #495057;
				color: #ffffff;
			}

			.table-stock {
				background: #495057;
				color: #ffffff;
			}

			.table-stock th,
			.table-stock td {
				border-color: #6c757d;
			}
		}
	</style>

</body>

</html>