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

$panel->setApp("employee", "Employee");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'employee');

$panel->setMeta(array(
	array("department", "Department", "far fa-user"),
	array("employee", "employee", "far fa-user"),
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
		'apps/employee/include/interface.js',
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
			case "department":
				include "control/controller.department.view.js";
				if ($os->allow("employee", "remove")) include "control/controller.department.remove.js";
				if ($os->allow("employee", "add")) include "control/controller.department.add.js";
				if ($os->allow("employee", "edit")) include "control/controller.department.edit.js";
				break;
			case "employee":
				include "control/controller.employee.view.js";
				if ($os->allow("employee", "remove")) include "control/controller.employee.remove.js";
				if ($os->allow("employee", "add")) include "control/controller.employee.add.js";
				if ($os->allow("employee", "edit")) include "control/controller.employee.edit.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>