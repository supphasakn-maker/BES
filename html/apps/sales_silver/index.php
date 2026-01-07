<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/session.php";


$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
//$os->initial_lang("lang");
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);

$rate_exchange = $os->load_variable("rate_exchange");
$rate_spot = $os->load_variable("rate_spot");
$rate_pmdc = $os->load_variable("rate_pmdc");

?>
<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Overview</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Announce Silver</a>
	</div>
</nav>
<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<div class="row gutters-sm">
			<div class="col-xl-12 mb-3">
				<?php
				?>
			</div>


		</div>
		<div class="row gutters-sm">
			<div class="col-sm-6">
				<?php include "view/card.customer.php"; ?>
				<?php include "view/card.ordertable.php"; ?>
			</div>
			<div class="col-sm-6">
				<?php include "view/card.quickorder.php"; ?>
				<?php include "view/card.quickbuyorder.php"; ?>
			</div>
		</div>
		<div class="row gutters-sm">
			<div class="col-sm-6">

			</div>
		</div>

	</div>
	<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					<?php
					// include "view/card.announce_silver.php";
					include "view/card.salesprice.php";
					?>
				</div>
			</div>
		</div>

	</div>
</div>
<script>
var plugins = [
    // CSS files ก่อน
    'plugins/datatables/dataTables.bootstrap4.min.css',
    'plugins/datatables/responsive.bootstrap4.min.css', 
    'plugins/select2/css/select2.min.css',
    'plugins/sweetalert/sweetalert.css',
    
    // JS files ตามลำดับ dependency
    'plugins/moment/moment.min.js',
    'plugins/chart.js/Chart.min.js',
    'plugins/canvas/html2canvas.min.js',  // แก้ path
    'plugins/jquery-sparkline/jquery.sparkline.min.js',
    'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
    'plugins/select2/js/select2.full.min.js', 
    'plugins/sweetalert/sweetalert-dev.js',
    
    // App specific JS
    'apps/customer/include/interface.js',
    'apps/sales/include/interface.js',
    'apps/sales_silver/include/interface.js'
]


	App.loadPlugins(plugins, null).then(() => {

		$('.select2').select2();
		<?php

		include "../customer/control/controller.customer.edit.js";
		include "control/controller.sales_silver.js";

		include "control/controller.order.view.js";
		if ($os->allow("sales_silver", "add")) include "../sales/control/controller.order.add.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.order.edit.js";
		if ($os->allow("sales_silver", "remove")) include "../sales/control/controller.order.remove.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.order.split.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.order.postpone.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.order.lock.js";
		if ($os->allow("sales_silver", "view")) include "../sales/control/controller.order.print.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.order.add_delivery.js";


		include "control/controller.quickorder.view.js";
		if ($os->allow("sales_silver", "add")) include "../sales/control/controller.quick_order.add.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.quick_order.edit.js";
		if ($os->allow("sales_silver", "remove")) include "../sales/control/controller.quick_order.remove.js";
		if ($os->allow("sales_silver", "edit")) include "../sales/control/controller.quick_order.transform.js";

		include "control/controller.quick_buyorder.add.js";
		if ($os->allow("sales_silver", "edit")) include "control/controller.quick_buyorder.edit.js";
		if ($os->allow("sales_silver", "remove")) include "control/controller.quick_buyorder.remove.js";

		?>
	}).then(() => App.stopLoading())
</script>