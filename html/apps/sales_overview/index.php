<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/demo.php";
include "../../include/session.php";
include "../../include/datastore.php";



$demo = new demo;
$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);

include_once "../../include/alert-buysell.php";

$panel->setApp("sales_overview", "Sales Overview");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'main');

$ui_form = new iform($dbc, $os->auth);

// ใช้วันที่จาก URL parameter หรือวันนี้
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Validate date format เพื่อความปลอดภัย
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !strtotime($date)) {
	$date = date("Y-m-d"); // fallback to today if invalid
}

$today = time();

// ใช้วันที่ที่ validated แล้วในการ query
$order_count = $dbc->GetRecord("bs_orders", "COUNT(id)", "DATE(date) = '" . $date . "'");
$order_total = $dbc->GetRecord("bs_orders", "SUM(amount)", "DATE(date) = '" . $date . "'");

switch ($panel->getView()) {
	case "delivery":
		include "view/page.delivery.php";
		break;
	default:
		include "view/page.main.php";
		break;
}

?>

<script>
	var plugins = [
		'apps/sales/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/sweetalert/sweetalert-dev.js',
		'plugins/sweetalert/sweetalert.css',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
	];

	App.loadPlugins(plugins, null).then(() => {
		App.checkAll();
		<?php
		if (date("Y-m-d", $today) == $date) {
			include "../sales/control/controller.order.remove_each.js";
		} else {
			if ($os->allow("sales", "special"))
				include "../sales/control/controller.order.remove_each.js";
		}
		?>
	}).then(() => App.stopLoading())
</script>