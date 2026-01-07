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
$panel = new ipanel($dbc, $os->auth);

$panel->setApp("audit", "Audit");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'match');

$panel->setMeta(array(
	array("match", "Match", "far fa-user"),
));
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
		'apps/audit/include/interface.js',
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
		switch ($panel->getView()) {
			case "match":
				include "control/controller.match.view.js";
				if ($os->allow("audit", "add")) include "control/controller.match.add.js";
				if ($os->allow("audit", "edit")) include "control/controller.match.edit.js";
				if ($os->allow("audit", "remove")) include "control/controller.match.remove.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>