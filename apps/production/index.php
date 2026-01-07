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

$panel->setApp("production", "Production");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'view');

$panel->setMeta(array(
	array("view", "Production", "far fa-user"),
	array("add", "Add", "far fa-plus"),
	array("edit", "Edit", "far fa-edit")
));

$panel->PageBreadcrumb();
?>
<div class="row">
	<div class="col-xl-12">
		<div class="card">
			<div class="card-body">
				<?php
				include "view/page." . $panel->getView() . ".php";
				?>
			</div>
		</div>
	</div>
</div>
<script>
	var plugins = [
		'apps/import/include/interface.js',
		'apps/production/include/interface.js',
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
			case "view":
				include "control/controller.produce.view.js";
				if ($os->allow("production", "add")) include "control/controller.produce.add.js";
				if ($os->allow("production", "edit")) include "control/controller.produce.edit.js";
				if ($os->allow("production", "approve")) include "control/controller.produce.approve.js";
				if ($os->allow("production", "approve")) include "control/controller.produce.deapprove.js";
				if ($os->allow("production", "approve")) include "control/controller.produce.unsubmit.js";
				if ($os->allow("production", "remove")) include "control/controller.produce.remove.js";
				break;
			case "add":
				if ($os->allow("production", "add")) include "control/controller.produce.add.js";
				if ($os->allow("production", "add")) include "control/controller.import.lookup.js";
				if ($os->allow("production", "add")) include "control/controller.file.upload.js";
				break;
			case "edit":
				if ($os->allow("production", "edit")) include "control/controller.produce.edit.js";
				if ($os->allow("production", "edit")) include "control/controller.import.lookup.js";
				if ($os->allow("production", "edit")) include "control/controller.file.upload.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>