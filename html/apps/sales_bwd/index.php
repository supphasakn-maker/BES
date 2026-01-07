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

$panel->setApp("sales_bwd", "Sales Bowins Design");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'order');

$panel->setMeta(array(
	array("order", "Order", "far fa-user"),
	array("delivery", "Delivery", "far fa-user"),
	array("quick_order", "Quick Order", "far fa-user"),
	array("packing", "Packing", "far fa-user"),
	array("spot", "Spot", "far fa-user")
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
		'apps/sales_bwd/include/interface.js',
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
			case "order":
				include "control/controller.order.view.js";
				if ($os->allow("sales_bwd", "add")) include "control/controller.order.add.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.order.edit.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.order.edit_tracking.js";
				if ($os->allow("sales_bwd", "remove")) include "control/controller.order.remove.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.order.postpone.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.order.lock.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.order.add_delivery.js";
				if ($os->allow("sales_bwd", "remove")) include "control/controller.order.remove_each.js";
				break;
			case "delivery":
				include "control/controller.delivery.view.js";
				// if($os->allow("sales","edit"))include "control/controller.delivery.combine.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.delivery.packing.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.delivery.payment.js";
				if ($os->allow("sales_bwd", "edit")) include "control/controller.delivery.billing.js";
				if ($os->allow("sales_bwd", "remove")) include "control/controller.delivery.remove.js";
				break;
			case "quickorder":
				// include "control/controller.quick_order.view.js";
				// if($os->allow("sales","add"))include "control/controller.quick_order.add.js";
				// if($os->allow("sales","edit"))include "control/controller.quick_order.edit.js";
				// if($os->allow("sales","remove"))include "control/controller.quick_order.remove.js";
				// if($os->allow("sales","approve"))include "control/controller.quick_order.transform.js";
				break;
			case "packing":
				// include "control/controller.packing.view.js";
				// if($os->allow("sales","add"))include "control/controller.packing.add.js";
				// if($os->allow("sales","edit"))include "control/controller.packing.edit.js";
				// if($os->allow("sales","remove"))include "control/controller.packing.remove.js";
				// if($os->allow("sales","approve"))include "control/controller.packing.approve.js";
				break;
			case "spot":
				// include "control/controller.spot.view.js";
				// if($os->allow("sales","add"))include "control/controller.spot.add.js";
				// if($os->allow("sales","edit"))include "control/controller.spot.edit.js";
				// if($os->allow("sales","remove"))include "control/controller.spot.remove.js";
				//if($os->allow("sales","approve"))include "control/controller.spot.approve.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>