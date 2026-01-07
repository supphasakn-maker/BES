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


?>
<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Stock Silver</a>
		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Stock อื่นๆ</a>
	</div>
</nav>
<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<div class="row gutters-sm">
		</div>
		<div class="row gutters-sm">
			<div class="col-sm-6">
				<?php include "view/card.stock_bowins.php"; ?>
			</div>
			<div class="col-sm-1">
				<?php include "view/card.stock_move.php"; ?>
			</div>
			<div class="col-sm-5">
				<?php include "view/card.stock_future.php"; ?>
			</div>
		</div>

	</div>
	<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">


		<?php include "view/card.silver.view.php"; ?>

	</div>
</div>

<script>
	var plugins = [
		'apps/customer/include/interface.js',
		'apps/stock_silver/include/interface.js',
		'apps/sales_silver/include/interface.js',
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

		$('.select2').select2();
		<?php
		include "control/controller.silver.view.js";
		include "control/controller.silver.add.js";
		include "control/controller.silver.add_multiple.js";
		include "control/controller.silver.remove.js";
		include "control/controller.silver.move.js";

		include "control/controller.future.view.js";
		include "control/controller.future.remove.js";
		include "control/controller.future.move.js";

		include "control/controller.scrap.add.js";
		include "control/controller.scrap.view.js";
		include "control/controller.scrap.remove.js";
		?>
	}).then(() => App.stopLoading())
</script>