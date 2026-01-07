<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
//$os->initial_lang("lang");
$panel = new ipanel($dbc, $os->auth);
$ui_form = new iform($dbc, $os->auth);


?>

<div class="row gutters-sm">
	<div class="col-xl-12 mb-3">
		<?php include "view/card.round.php"; ?>
	</div>
</div>
<script>
	var plugins = [
		'apps/production_round/include/interface.js',
		'apps/engine/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
		'plugins/select2/js/select2.full.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/moment/moment.min.js'
	]


	App.loadPlugins(plugins, null).then(() => {

		<?php
		include "control/controller.round.view.js";
		// include "control/controller.spot.info.js";
		if($os->allow("production_round","edit"))include "control/controller.round.split.js";
		if($os->allow("production_round","edit"))include "control/controller.round.approve.js";
		// if($os->allow("defer_split","remove"))include "control/controller.split.remove.js";

		?>
	}).then(() => App.stopLoading())
</script>