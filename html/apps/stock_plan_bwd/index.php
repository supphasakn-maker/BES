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
<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Overview</a>
	</div>
</nav>
<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<div class="row gutters-sm">
			<div class="col-xl-12 mb-3">
				<?php
				include "view/card.stock.php";
				?>
			</div>
		</div>
		<div class="row gutters-sm">
			<div class="col-xl-12 mb-3">
				<?php include "view/card.stock_now.php"; ?>
			</div>
		</div>
		<div class="row gutters-sm">
			<div class="col-xl-12 mb-3">
				<?php include "view/card.stock_box.php"; ?>
			</div>
		</div>
		<div class="row gutters-sm">
			<div class="col-xl-12 mb-3">
				<?php include "view/card.stock_overview.php"; ?>
			</div>
		</div>
	</div>
</div>


<script>
	var plugins = [
		'apps/stock_plan_bwd/include/interface.js',
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

		// include "../customer/control/controller.customer.edit.js";
		// include "control/controller.sales_screen.js";
		// include "control/controller.order.add.js";


		// include "control/controller.order.view.js";
		// if($os->allow("sales_screen","add"))include "../sales/control/controller.order.add.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.order.edit.js";
		// if($os->allow("sales_screen","remove"))include "../sales/control/controller.order.remove.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.order.split.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.order.postpone.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.order.lock.js";
		// if($os->allow("sales_screen","view"))include "../sales/control/controller.order.print.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.order.add_delivery.js";

		// include "control/controller.quickorder.view.js";
		// if($os->allow("sales_screen","add"))include "../sales/control/controller.quick_order.add.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.quick_order.edit.js";
		// if($os->allow("sales_screen","remove"))include "../sales/control/controller.quick_order.remove.js";
		// if($os->allow("sales_screen","edit"))include "../sales/control/controller.quick_order.transform.js";


		?>
	}).then(() => App.stopLoading())
</script>