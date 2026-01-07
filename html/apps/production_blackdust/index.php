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
$panel = new ipanel($dbc, $os->auth);

$panel->setApp("production_blackdust", "Prepare");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'prepare');
$panel->setSection(isset($_GET['section']) ? $_GET['section'] : '');
$ui_form = new iform($dbc, $os->auth);

$panel->setMeta(array(
	array("prepare", "Round Refine", "far fa-user"),
));
?>
<?php
$panel->PageBreadcrumb();
?>
<div class="row">
	<div class="col-xl-12">
		<?php
		$panel->EchoInterface();
		?>
	</div>
</div>
<script>
	var plugins = [
		'apps/production_blackdust/include/interface.js',
		'apps/production/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/select2/js/select2.min.js',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/moment/moment.min.js'
	];
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
		<?php
		switch ($panel->getView()) {
			case "prepare":
				include "control/controller.prepare.view.js";
				if ($os->allow("production_blackdust", "add")) include "control/controller.prepare.add.js";
				if ($os->allow("production_blackdust", "edit")) include "control/controller.prepare.edit.js";
				if ($os->allow("production_blackdust", "remove")) include "control/controller.prepare.remove.js";
				if ($os->allow("production_blackdust", "add")) include "control/controller.prepare.add_scrap.js";
				if ($os->allow("production_blackdust", "edit")) include "control/controller.import.lookup.js";
				if ($os->allow("production_blackdust", "edit")) include "../production/control/controller.file.upload.js";
				if ($os->allow("production_blackdust", "remove")) include "control/controller.scrap.remove.js";

				if ($os->allow("production_blackdust", "add")) include "control/controller.prepare.add_oven.js";
				if ($os->allow("production_blackdust", "remove")) include "control/controller.oven.remove.js";
				if ($os->allow("production_blackdust", "add")) include "control/controller.prepare.add_scale.js";
				if ($os->allow("production_blackdust", "remove")) include "control/controller.scale.remove.js";
				if ($os->allow("production_blackdust", "add")) include "control/controller.prepare.add_furnace.js";
				if ($os->allow("production_blackdust", "remove")) include "control/controller.furnace.remove.js";

				if ($os->allow("production_blackdust", "remove")) include "control/controller.pack.remove.js";


				if ($os->allow("production_blackdust", "approve")) include "control/controller.prepare.approve.js";
				// if($os->allow("production_prepare","edit"))include "../production/control/controller.file.upload.js";

				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>