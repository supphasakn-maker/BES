<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/session.php";
include "../../include/datastore.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);

include_once "../../include/alert-buysell.php";

$today = time();
?>

<style>
	.dashboard-section {
		margin-bottom: 2rem;
	}

	.section-header {
		background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
		border: none;
		border-radius: 12px;
		padding: 1.25rem 1.5rem;
		margin-bottom: 1.5rem;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
	}

	.section-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.1rem;
		color: #2c3e50;
	}

	.section-header .section-subtitle {
		color: #6c757d;
		font-size: 0.875rem;
		margin-top: 0.25rem;
		margin-bottom: 0;
	}

	.dashboard-card {
		border: none;
		border-radius: 12px;
		box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
		transition: all 0.3s ease;
		margin-bottom: 1.5rem;
		overflow: hidden;
	}

	.dashboard-card:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 25px rgba(0, 0, 0, 0.12);
	}

	.dashboard-card .card-body {
		padding: 1.5rem;
	}

	.category-icon {
		width: 40px;
		height: 40px;
		border-radius: 10px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		margin-right: 0.75rem;
		font-size: 1.1rem;
	}

	.icon-sales {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
	}

	.icon-total {
		background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
		color: white;
	}

	.icon-production {
		background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
		color: white;
	}

	.icon-trader {
		background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
		color: white;
	}

	.icon-finance {
		background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
		color: #8b4513;
	}

	.row-spacing {
		margin-bottom: 2rem;
	}

	.empty-space {
		min-height: 1px;
	}

	@media (max-width: 768px) {
		.section-header {
			padding: 1rem 1.25rem;
		}

		.section-header h4 {
			font-size: 1rem;
		}

		.dashboard-card .card-body {
			padding: 1.25rem;
		}
	}

	.summary-alert {
		background: #00204E;
		border: none;
		border-radius: 12px;
		color: white;
		padding: 1.5rem;
		text-align: center;
		font-weight: 500;
		box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
	}
</style>

<!-- Dashboard Header -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-sales">
				<i class="fas fa-tachometer-alt"></i>
			</div>
			<div>
				<h4>Dashboard Overview</h4>
				<p class="section-subtitle">ภาพรวมข้อมูลประจำวัน <?php echo date("d/m/Y", $today); ?></p>
			</div>
		</div>
	</div>
</div>

<!-- Today's Sales Section -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-sales">
				<i class="fas fa-chart-line"></i>
			</div>
			<div>
				<h4>Today's Sales</h4>
				<p class="section-subtitle">ยอดขายประจำวันนี้ BWS และ BWD</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_total.php"; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_bwd.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_total_bwd_kg.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_total_bwd.php"; ?>
				</div>
			</div>
		</div>

	</div>


	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_silver.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_lbma.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_recycle.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_silver_bar.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_originthai.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_plate.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_article.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="empty-space"></div>
		</div>
	</div>
</div>

<!-- Total Sales Section -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-total">
				<i class="fas fa-chart-bar"></i>
			</div>
			<div>
				<h4>Total Sales</h4>
				<p class="section-subtitle">ยอดขายรวมรายเดือน รายปี และรายได้</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_amount.php"; ?>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.revenue.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_amount_bwd_month.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_amount_bwd.php"; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_bwd_kg_month.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_amount_bwd_kg_year.php"; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_year.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_amount_bwd_year.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.revenue_year.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.sales_year_bwd.php"; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Finance Section -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-finance">
				<i class="fas fa-money-bill-wave"></i>
			</div>
			<div>
				<h4>Finance</h4>
				<p class="section-subtitle">ข้อมูลการเงินและยอดเงินรอรับชำระ</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.pending_payment.php"; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Productions Section -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-production">
				<i class="fas fa-industry"></i>
			</div>
			<div>
				<h4>Productions</h4>
				<p class="section-subtitle">ข้อมูลการผลิตและการจัดส่ง</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.delivery.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.production.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="empty-space"></div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="empty-space"></div>
		</div>
	</div>
</div>

<!-- Trader Section -->
<div class="dashboard-section">
	<div class="section-header">
		<div class="d-flex align-items-center">
			<div class="category-icon icon-trader">
				<i class="fas fa-users"></i>
			</div>
			<div>
				<h4>Trader</h4>
				<p class="section-subtitle">ข้อมูลยอดคงเหลือและการซื้อขาย</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.balance_silver.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="card dashboard-card">
				<div class="card-body">
					<?php include "view/widget.balance_fx.php"; ?>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="empty-space"></div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div class="empty-space"></div>
		</div>
	</div>
</div>

<!-- Summary Section -->
<div class="dashboard-section">
	<div class="alert summary-alert">
		<i class="fas fa-clock me-2"></i>
		<strong>รอข้อมูลสรุป</strong> - ระบบกำลังประมวลผลข้อมูลเพิ่มเติม
	</div>
</div>

<script>
	// Safe sparkline initialization function
	function initSafeSparkline(selector, options = {}) {
		const defaultOptions = {
			type: 'line',
			barWidth: 8,
			height: 20,
			barColor: '#007bff',
			nullColor: '#cccccc',
			zeroColor: '#cccccc',
			lineColor: '#007bff',
			fillColor: false,
			spotColor: false,
			minSpotColor: false,
			maxSpotColor: false,
			highlightSpotColor: false,
			highlightLineColor: false
		};

		const config = Object.assign({}, defaultOptions, options);

		try {
			const element = $(selector);
			if (element.length === 0) {
				console.warn(`Sparkline element not found: ${selector}`);
				return false;
			}

			// Get data from element
			let rawData = element.data('sparkline-data') || element.text() || element.html() || '';

			// Clean and parse data
			let values = [];

			if (typeof rawData === 'string') {
				// Remove all non-numeric characters except comma, dot, and minus
				rawData = rawData.replace(/[^0-9,.\-\s]/g, '');

				// Split by comma or space and clean each value
				values = rawData.split(/[,\s]+/).map(val => {
					const trimmed = val.trim();
					if (trimmed === '' || trimmed === '-' || trimmed === 'null' || trimmed === 'undefined') {
						return 0;
					}
					const parsed = parseFloat(trimmed);
					return isNaN(parsed) ? 0 : parsed;
				}).filter(val => val !== '');
			}

			// Only initialize if we have valid data
			if (values.length > 0) {
				element.sparkline(values, config);
				return true;
			} else {
				console.warn(`No valid data for sparkline: ${selector}`);
				return false;
			}

		} catch (error) {
			console.error(`Failed to initialize sparkline for ${selector}:`, error);
			return false;
		}
	}

	var plugins = [
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
		'plugins/select2/js/select2.full.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/moment/moment.min.js'
	];

	App.loadPlugins(plugins, null).then(() => {
		$(() => {
			// Define colors
			const blue = '#007bff';
			const red = '#dc3545';
			const green = '#28a745';
			const cyan = '#17a2b8';
			const purple = '#9932CC';
			const dark = '#191919';

			// Initialize Sparklines safely
			const sparklineConfigs = [{
					id: '#connections',
					color: blue,
					type: 'bar'
				},
				{
					id: '#total_sales',
					color: red,
					type: 'bar'
				},
				{
					id: '#total_sales_bwd',
					color: red,
					type: 'bar'
				},
				{
					id: '#sales_silver',
					color: blue,
					type: 'line'
				},
				{
					id: '#sales_lbma',
					color: dark,
					type: 'line'
				},
				{
					id: '#sales_silver_bar',
					color: purple,
					type: 'line'
				},
				{
					id: '#amount_month',
					color: blue,
					type: 'bar'
				},
				{
					id: '#amount_year',
					color: blue,
					type: 'bar'
				},
				{
					id: '#amount_month_bwd',
					color: blue,
					type: 'line'
				},
				{
					id: '#amount_month_kg_bwd',
					color: blue,
					type: 'line'
				},
				{
					id: '#amount_year_bwd',
					color: blue,
					type: 'line'
				},
				{
					id: '#amount_year_bwd_kg',
					color: blue,
					type: 'line'
				},
				{
					id: '#sales_recycle',
					color: green,
					type: 'pie'
				},
				{
					id: '#sales_originthai',
					color: green,
					type: 'pie'
				},
				{
					id: '#sales_plate',
					color: green,
					type: 'bar'
				},
				{
					id: '#sales_article',
					color: green,
					type: 'bar'
				},
				{
					id: '#sale_total_month',
					color: cyan,
					type: 'pie'
				},
				{
					id: '#sale_total_year',
					color: cyan,
					type: 'pie'
				},
				{
					id: '#sale_total_bwd_month',
					color: cyan,
					type: 'pie'
				},
				{
					id: '#sale_total_bwd_year',
					color: cyan,
					type: 'pie'
				},
				{
					id: '#bwd',
					color: blue,
					type: 'bar'
				},
				{
					id: '#bwd_kg',
					color: blue,
					type: 'bar'
				},
				{
					id: '#yourArticles',
					color: red,
					type: 'bar'
				},
				{
					id: '#yourPhotos',
					color: green,
					type: 'bar'
				}
			];

			// Initialize all sparklines
			sparklineConfigs.forEach(config => {
				initSafeSparkline(config.id, {
					type: config.type,
					barColor: config.color,
					lineColor: config.color,
					barWidth: 8,
					height: 20
				});
			});
		});
	}).then(() => App.stopLoading());
</script>