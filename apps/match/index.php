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

$panel->setApp("match", "Match");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'silver');

$panel->setMeta(array(
	array("silver", "Silver", "far fa-user"),
	array("usd", "USD", "far fa-user"),
	array("fifosilver", "FIFOSilver", "far fa-user"),
	array("fifousd", "FIFOUSD", "far fa-user"),
	array("overview", "Overview", "far fa-user"),
	array("profit", "Profit", "far fa-user"),
	array("engine", "engine", "far fa-wrench"),
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
		'apps/match/include/interface.js',
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
			case "silver":
				include "control/controller.silver.view.js";
				if ($os->allow("match", "edit")) include "control/controller.silver.remark.js";
				if ($os->allow("match", "add")) include "control/controller.silver.match.js";
				if ($os->allow("match", "edit")) include "control/controller.silver.unmatch.js";
				if ($os->allow("match", "view")) include "control/controller.silver.lookup.js";
				if ($os->allow("match", "view")) include "control/controller.silver.filter.js";
				break;
			case "usd":
				include "control/controller.usd.view.js";
				if ($os->allow("match", "edit")) include "control/controller.usd.remark.js";
				if ($os->allow("match", "add")) include "control/controller.usd.match.js";
				if ($os->allow("match", "edit")) include "control/controller.usd.unmatch.js";
				if ($os->allow("match", "edit")) include "control/controller.usd.lookup.js";
				if ($os->allow("match", "edit")) include "control/controller.usd.filter.js";
				break;
			case "fifosilver":
				include "control/controller.fifosilver.view.js";
				if ($os->allow("match", "edit")) include "control/controller.fifosilver.lookup.js";
				if ($os->allow("match", "edit")) include "control/controller.fifosilver.search.js";
				if ($os->allow("match", "edit")) include "control/controller.fifosilver.filter.js";
				break;
			case "fifousd":
				include "control/controller.fifousd.view.js";
				if ($os->allow("match", "edit")) include "control/controller.fifousd.lookup.js";
				if ($os->allow("match", "edit")) include "control/controller.fifousd.search.js";
				if ($os->allow("match", "edit")) include "control/controller.fifousd.filter.js";
				break;
			case "overview":
				include "control/controller.overview.view.js";
				include "control/controller.overview.search.js";
				if ($os->allow("match", "edit")) include "control/controller.overview.filter.js";
				break;
			case "profit":
				include "control/controller.profit.search.js";
				break;
			case "engine":
				if ($os->allow("match", "edit")) include "control/controller.engine.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>