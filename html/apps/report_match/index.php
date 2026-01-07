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

$panel->setApp("report_match", "Match");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'report_match');
$panel->setMeta(array(
	array("report_match", "All Supplier", "far fa-user"),
	array("report_standard", "Standard", "far fa-user"),
	array("report_jinsung", "Supplier Adjust Cost", "far fa-user"),
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
		// include "control/controller.view.js";
		switch ($panel->getView()) {
			case "report_match":
				include "control/controller.view.js";
				break;
			case "report_standard":
				include "control/controller_standard.view.js";
				break;
			case "report_jinsung":
				include "control/controller_jinsung.view.js";
				break;
		}
		?>

	}).then(() => App.stopLoading())
</script>