<?php
session_start();
@ini_set('display_errors', 1);
include "../../config/define.php";
include "../../include/db.php";
include "../../include/oceanos.php";
include "../../include/iface.php";

$dbc = new dbc;
$dbc->Connect();
$os = new oceanos($dbc);
$panel = new ipanel($dbc, $os->auth);

$panel->setApp("claim", "Claim");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'product');
$panel->setSection(isset($_GET['section']) ? $_GET['section'] : '');
$ui_form = new iform($dbc, $os->auth);

$panel->setMeta(array(
	array("product", "Product", "fas fa-bullhorn"),
));
?>
<?php
$panel->PageBreadcrumb();

if ($panel->getView() == "printable") {
	include "view/page.product.view.php";
} else {
?>
	<div class="row">
		<div class="col-xl-12">
			<?php
			$panel->EchoInterface();
			?>
		</div>
	</div>
<?php
}
?>
<script>
	var plugins = [
		'apps/claim/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/chart.js/Chart.min.js',
		'plugins/jquery-sparkline/jquery.sparkline.min.js',
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
			case "product":
				include "control/controller.product.view.js";
				if ($os->allow("claim", "add")) include "control/controller.product.add.js";
				if ($os->allow("claim", "edit")) include "control/controller.product.edit.js";
				if ($os->allow("claim", "edit")) include "control/controller.file.upload.js";
				if ($os->allow("claim", "remove")) include "control/controller.product.remove.js";
				if ($os->allow("claim", "approve")) include "control/controller.product.approve.js";
				if ($os->allow("claim", "approve")) include "control/controller.product.reject.js";
				if ($os->allow("claim", "edit")) include "control/controller.product.submit.js";
				if ($os->allow("claim", "approve")) include "control/controller.product.solve.js";
				if ($os->allow("claim", "approve")) include "control/controller.product.confirm.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>