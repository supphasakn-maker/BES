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

$panel->setApp("usd", "USD");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'purchase');

$panel->setMeta(array(
	array("purchase", "Purchase", "far fa-user"),
	array("pending", "Pending", "far fa-user"),
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
		'apps/usd/include/interface.js',
		'apps/purchase/include/interface.js',
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
			case "purchase":
				include "control/controller.purchase.view.js";
				include "../purchase/control/controller.usd.split.js";
				if ($os->allow("usd", "add")) include "../purchase/control/controller.usd.add.js";
				if ($os->allow("usd", "edit")) include "../purchase/control/controller.usd.edit.js";
				if ($os->allow("spot_silver", "remove")) include "../purchase/control/controller.usd.remove.js";
				break;
			case "pending":
				include "control/controller.pending.view.js";
				if ($os->allow("usd", "add")) include "../purchase/control/controller.usd.add.js";
				if ($os->allow("usd", "edit")) include "../purchase/control/controller.usd.edit.js";
				if ($os->allow("spot_silver", "remove")) include "../purchase/control/controller.usd.remove.js";
				if ($os->allow("spot_silver", "approve")) include "../purchase/control/controller.usd.purchase.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>