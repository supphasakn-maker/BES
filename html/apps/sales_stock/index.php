<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";
include "../../include/demo.php";
include "../../include/session.php";

$demo = new demo;
$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
//$os->initial_lang("lang");
$panel = new ipanel($dbc, $os->auth);
$panel->setApp("sales_stock", "Sales Stock");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'main');

$ui_form = new iform($dbc, $os->auth);


switch ($panel->getView()) {
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



	}).then(() => App.stopLoading())
</script>