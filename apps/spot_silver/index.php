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

$panel->setApp("spot_silver", "Spot Silver");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'purchase');



$panel->setMeta(array(
	array("purchase", "Purchase", "far fa-user"),
	array("pending", "Pending", "far fa-user"),
	array("claim", "Claim", "far fa-user"),
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
		'apps/purchase/include/interface.js',
		'apps/spot_silver/include/interface.js',
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
				if ($os->allow("spot_silver", "add")) include "../purchase/control/controller.spot.add.js";
				if ($os->allow("spot_silver", "edit")) include "../purchase/control/controller.spot.edit.js";
				if ($os->allow("spot_silver", "remove")) include "../purchase/control/controller.spot.remove.js";
				break;
			case "pending":
				include "control/controller.pending.view.js";
				if ($os->allow("spot_silver", "add")) include "../purchase/control/controller.spot.add.js";
				if ($os->allow("spot_silver", "edit")) include "../purchase/control/controller.spot.edit.js";
				if ($os->allow("spot_silver", "remove")) include "../purchase/control/controller.spot.remove.js";
				if ($os->allow("spot_silver", "approve")) include "../purchase/control/controller.spot.purchase.js";
				break;
			case "claim":
				include "control/controller.claim.view.js";
				if ($os->allow("spot_silver", "add")) include "control/controller.claim.add.js";
				if ($os->allow("spot_silver", "edit")) include "../purchase/control/controller.spot.edit.js";
				if ($os->allow("spot_silver", "remove")) include "../purchase/control/controller.spot.remove.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>