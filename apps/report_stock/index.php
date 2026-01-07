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
$ui_form = new iform($dbc, $os->auth);

$panel->setApp("report_stock", "Stock Report");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'main');

?>
<div class="card">
	<div class="card-body">
		<?php include "include/const.php"; ?>
		<?php include "view/view.header.php"; ?>
		<hr>
		<div id="output">
		</div>
	</div>
	<script>
		var plugins = [
			'apps/report_stock/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'plugins/sweetalert/sweetalert-dev.js',
			'plugins/sweetalert/sweetalert.css',
			'plugins/moment/moment.min.js'
		];
		App.loadPlugins(plugins, null).then(() => {
			App.checkAll()
			<?php
			include "control/controller.report.js";
			?>
		}).then(() => App.stopLoading())
	</script>