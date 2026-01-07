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

$panel->setApp("reserve_silver", "Reserve Silver");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'stock');

$panel->setMeta(array(
	array("stock", "Stock Reserve", "far fa-user"),
	array("reserve", "Reserve", "far fa-user"),
	array("imported", "Imported", "far fa-user"),
	array("history", "History", "far fa-user"),
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
		'apps/reserve_silver/include/interface.js',
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
			case "reserve":
				include "control/controller.reserve.view.js";

				if ($os->allow("reserve_silver", "add")) include "control/controller.reserve.add.js";
				if ($os->allow("reserve_silver", "edit")) include "control/controller.reserve.edit.js";
				if ($os->allow("reserve_silver", "remove")) include "control/controller.reserve.remove.js";

				break;
			case "imported":
				include "control/controller.imported.view.js";

				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>