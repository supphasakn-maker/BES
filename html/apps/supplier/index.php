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

$panel->setApp("supplier", "Supplier");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'supplier');

$panel->setMeta(array(
	array("group", "Group", "far fa-user"),
	array("supplier", "Supplier", "far fa-user"),
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
		'apps/supplier/include/interface.js',
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
			case "group":
				include "control/controller.group.view.js";
				if ($os->allow("supplier", "remove")) include "control/controller.group.remove.js";
				if ($os->allow("supplier", "add")) include "control/controller.group.add.js";
				if ($os->allow("supplier", "edit")) include "control/controller.group.edit.js";
				break;
			case "supplier":
				include "control/controller.supplier.view.js";
				if ($os->allow("supplier", "remove")) include "control/controller.supplier.remove.js";
				if ($os->allow("supplier", "add")) include "control/controller.supplier.add.js";
				if ($os->allow("supplier", "edit")) include "control/controller.supplier.edit.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>