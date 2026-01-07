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

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

$rate_pmdc_recycle = $os->load_variable("rate_pmdc_recycle");

$rate_recycle1 = $os->load_variable("rate_recycle1");
$rate_recycle2 = $os->load_variable("rate_recycle2");

?>


<style>
	/* Responsive Styles */
	@media (max-width: 768px) {

		/* Mobile Navigation */
		.nav-tabs {
			flex-direction: column;
			border-bottom: none;
		}

		.nav-tabs .nav-link {
			text-align: center;
			border: 1px solid #dee2e6;
			border-radius: 0;
			margin-bottom: 2px;
		}

		.nav-tabs .nav-link.active {
			border-color: #007bff;
			background-color: #007bff;
			color: white;
		}

		/* Mobile Grid Adjustments */
		.col-sm-6 {
			margin-bottom: 1rem;
		}

		/* Card Responsiveness */
		.card {
			margin-bottom: 1rem;
		}

		.card-body {
			padding: 0.75rem;
		}

		/* Table Responsiveness */
		.table-responsive {
			font-size: 0.875rem;
		}

		/* Form Responsiveness */
		.form-group {
			margin-bottom: 0.75rem;
		}

		.btn {
			width: 100%;
			margin-bottom: 0.5rem;
		}

		.btn-group .btn {
			width: auto;
		}
	}

	@media (max-width: 576px) {

		/* Extra Small Devices */
		.container-fluid {
			padding-left: 0.5rem;
			padding-right: 0.5rem;
		}

		.card-header {
			padding: 0.5rem;
			font-size: 0.9rem;
		}

		.nav-tabs .nav-link {
			padding: 0.5rem 0.75rem;
			font-size: 0.9rem;
		}

		/* Hide non-essential columns on very small screens */
		.table .d-none-xs {
			display: none !important;
		}

		/* Stack buttons vertically */
		.btn-toolbar .btn-group {
			display: block;
			width: 100%;
			margin-bottom: 0.5rem;
		}
	}

	@media (min-width: 769px) and (max-width: 1024px) {

		/* Tablet Adjustments */
		.col-xl-12 {
			margin-bottom: 1.5rem;
		}

		.gutters-sm>[class*="col-"] {
			padding-left: 0.75rem;
			padding-right: 0.75rem;
		}
	}

	/* Enhanced Table Responsiveness */
	.table-responsive-stack tr>td {
		white-space: nowrap;
	}

	@media (max-width: 768px) {
		.table-responsive-stack {
			border: 0;
		}

		.table-responsive-stack thead {
			display: none;
		}

		.table-responsive-stack tr {
			border: 1px solid #ccc;
			display: block;
			margin-bottom: 0.5rem;
			padding: 0.5rem;
			border-radius: 0.25rem;
		}

		.table-responsive-stack td {
			border: none;
			display: block;
			text-align: left;
			padding: 0.25rem 0;
		}

		.table-responsive-stack td:before {
			content: attr(data-label) ": ";
			font-weight: bold;
			display: inline-block;
			width: 40%;
		}
	}

	/* Chart Responsiveness */
	.chart-container {
		position: relative;
		height: 300px;
		width: 100%;
	}

	@media (max-width: 768px) {
		.chart-container {
			height: 250px;
		}
	}

	@media (max-width: 576px) {
		.chart-container {
			height: 200px;
		}
	}

	/* Select2 Responsiveness */
	@media (max-width: 768px) {
		.select2-container {
			width: 100% !important;
		}

		.select2-container .select2-selection--single {
			height: calc(1.5em + 0.75rem + 2px);
		}
	}

	/* Loading Overlay Responsiveness */
	@media (max-width: 768px) {
		.loading-overlay {
			font-size: 0.9rem;
		}
	}
</style>
<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
			<i class="fas fa-chart-line d-inline d-sm-none"></i>
			<span class="d-none d-sm-inline">Overview</span>
		</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
			<i class="fas fa-shopping-cart d-inline d-sm-none"></i>
			<span class="d-none d-sm-inline">Order</span>
		</a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<div class="row gutters-sm">
			<div class="col-12 mb-3">
				<?php include "view/card.stock.php"; ?>
			</div>
		</div>

		<div class="row gutters-sm">
			<div class="col-lg-6 col-md-12 mb-3">
				<?php include "view/card.customer.php"; ?>
			</div>
			<div class="col-lg-6 col-md-12 mb-3">
				<?php include "view/card.spot.php"; ?>
			</div>
		</div>

		<div class="row gutters-sm">
			<div class="col-lg-6 col-md-12 mb-3">
				<?php include "view/card.ordertable.php"; ?>
			</div>
			<div class="col-lg-6 col-md-12 mb-3">
				<?php include "view/card.quickorder.php"; ?>
			</div>
		</div>
	</div>

	<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
		<div class="row">
			<div class="col-12">
				<?php include "view/card.order.add.php"; ?>
			</div>
		</div>
	</div>
</div>

<script>
	var plugins = [
		'apps/customer/include/interface.js',
		'apps/sales/include/interface.js',
		'apps/sales_screen/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
		'plugins/select2/js/select2.full.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/moment/moment.min.js'
	]


	App.loadPlugins(plugins, null).then(() => {
		/*
		$('.datatable').DataTable({
			"dom": '<"toolbar">rtp',
			"pageLength": 5
		});
		*/

		$('.select2').select2();
		<?php

		include "../customer/control/controller.customer.edit.js";
		include "control/controller.sales_screen.js";
		include "control/controller.order.add.js";


		include "control/controller.order.view.js";
		if ($os->allow("sales_screen", "add")) include "../sales/control/controller.order.add.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.order.edit.js";
		if ($os->allow("sales_screen", "remove")) include "../sales/control/controller.order.remove.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.order.split.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.order.postpone.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.order.lock.js";
		if ($os->allow("sales_screen", "view")) include "../sales/control/controller.order.print.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.order.add_delivery.js";

		include "control/controller.quickorder.view.js";
		if ($os->allow("sales_screen", "add")) include "../sales/control/controller.quick_order.add.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.quick_order.edit.js";
		if ($os->allow("sales_screen", "remove")) include "../sales/control/controller.quick_order.remove.js";
		if ($os->allow("sales_screen", "edit")) include "../sales/control/controller.quick_order.transform.js";


		?>
	}).then(() => App.stopLoading())
</script>