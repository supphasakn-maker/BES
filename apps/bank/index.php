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

$panel->setApp("bank", "Bank");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'statement');

$panel->setMeta(array(
	array("statement", "Statement", "far fa-user"),
	array("static", "Static", "far fa-user"),
	array("dailyreport", "Daily Report", "far fa-user"),
	array("dailyreport1", "Daily Report1", "far fa-user"),
	array("weeklyreport", "Weekly Report", "far fa-user"),
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
		'apps/bank/include/interface.js',
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
			case "dailyreport":
				include "control/controller.dailyreport.view.js";
				break;
			case "dailyreport1":
				include "control/controller.dailyreport1.view.js";
				break;
			case "weeklyreport":
				include "control/controller.weeklyreport.view.js";
				break;
			case "statement":
				include "control/controller.statement.view.js";
				if ($os->allow("bank", "add")) include "control/controller.statement.add.js";
				if ($os->allow("bank", "edit")) include "control/controller.statement.edit.js";
				if ($os->allow("bank", "remove")) include "control/controller.statement.remove.js";
				if ($os->allow("bank", "edit")) include "control/controller.statement.mapping.js";
				if ($os->allow("bank", "edit")) include "control/controller.statement.import.js";
				break;
			case "static":
				include "control/controller.static.view.js";
				if ($os->allow("bank", "add")) include "control/controller.static.add.js";
				if ($os->allow("bank", "edit")) include "control/controller.static.edit.js";
				if ($os->allow("bank", "remove")) include "control/controller.static.remove.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>