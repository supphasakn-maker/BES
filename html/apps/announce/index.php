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

$panel->setApp("announce", "Announce");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'announce_silver');

$panel->setMeta(array(
	array("announce_silver", "Announce Silver", "far fa-user"),
	array("difference", "Difference", "far fa-user"),

));
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
		'apps/announce/include/interface.js',
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
			case "announce_silver":
				include "control/controller.announce_silver.view.js";
				include "control/controller.announce_silver.add.js";
				include "control/controller.announce_silver.edit.js";
				include "control/controller.announce_silver.remove.js";
				include "control/controller.announce_silver.recalculate.js";
				include "control/controller.announce_silver.recal.js";
				break;
			case "difference":
				include "control/controller.difference.edit.js";
				include "control/controller.difference.pmdc_change.js";
				include "control/controller.difference.change_buy.js";
				include "control/controller.difference.pmdc_grains.js";
		}
		?>
	}).then(() => App.stopLoading())
</script>