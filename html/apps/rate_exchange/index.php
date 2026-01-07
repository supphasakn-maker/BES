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

$panel->setApp("rate_exchange", "Rate Exchange");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'master');

$panel->setMeta(array(
	array("master", "Master", "far fa-user"),
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
		'apps/rate_exchange/include/interface.js',
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
			case "master":
				if ($os->allow("rate_exchange", "edit")) include "control/controller.master.change_scb.js";
				if ($os->allow("rate_exchange", "edit")) include "control/controller.master.change_kbank.js";
				if ($os->allow("rate_exchange", "edit")) include "control/controller.master.change_bbl.js";
				if ($os->allow("rate_exchange", "edit")) include "control/controller.master.change_bay.js";

				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>