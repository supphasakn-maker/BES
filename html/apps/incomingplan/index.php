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

$panel->setApp("incomingplan", "Incomingplan");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'plan');

$panel->setMeta(array(
	array("plan", "Plan", "far fa-user"),
	array("show", "Show", "far fa-view"),
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
		'apps/incomingplan/include/interface.js',
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
			case "show":
				include "control/controller.show.js";
			case "plan":
				include "control/controller.plan.view.js";
				if ($os->allow("incomingplan", "add")) include "control/controller.plan.add.js";
				if ($os->allow("incomingplan", "edit")) include "control/controller.plan.edit.js";
				if ($os->allow("incomingplan", "remove")) include "control/controller.plan.remove.js";
				if ($os->allow("incomingplan", "view")) include "control/controller.plan.lookup.js";
				if ($os->allow("incomingplan", "edit")) include "control/controller.plan.split.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>
<style>
	#tblPlan:overflow-x: scroll;
</style>