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

$panel->setApp("defer_adjust", "Defer Adjust");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'deposit');

$panel->setMeta(array(
	// array("defer", "Fund in - ปิด Defer", "far fa-clipboard"),
	array("adjust", "Sell Adj.STD", "far fa-building"),
	array("purchase", "Purchase Adjust Cost", "far fa-money-bill"),
	array("physical", "Fund in - ปิด Adjust", "far fa-money-bill"),
	array("deposit", "Deposit", "far fa-money-bill"),
	array("usd", "FCD - USD", "far fa-money-bill"),

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
		'apps/defer_adjust/include/interface.js',
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
			// case "defer":
			// 	include "control/controller.defer.view.js";
			// 	include "control/controller.defer.add.js";
			// 	include "control/controller.defer.edit.js";
			// 	include "control/controller.defer.remove.js";
			// 	break;
			case "adjust":
				include "control/controller.adjust.view.js";
				include "control/controller.adjust.add.js";
				include "control/controller.adjust.edit.js";
				include "control/controller.adjust.remove.js";
				break;
			case "purchase":
				include "control/controller.purchase.view.js";
				include "control/controller.purchase.add.js";
				include "control/controller.purchase.edit.js";
				include "control/controller.purchase.remove.js";
				break;
			case "physical":
				include "control/controller.physical_adjust.view.js";
				include "control/controller.physical_adjust.add.js";
				include "control/controller.physical_adjust.edit.js";
				include "control/controller.physical_adjust.remove.js";
				break;
			case "deposit":
				include "control/controller.deposit.view.js";
				include "control/controller.deposit.add.js";
				include "control/controller.deposit.remove.js";
				include "control/controller.deposit.edit.js";
				break;
			case "usd":
				include "control/controller.usd.view.js";
				include "control/controller.usd.add.js";
				include "control/controller.usd.remove.js";
				include "control/controller.usd.edit.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>