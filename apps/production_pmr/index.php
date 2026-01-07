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

$panel->setApp("production_pmr", "Pmr");
$panel->setView(isset($_GET['view']) ? $_GET['view'] : 'pmr');
$panel->setSection(isset($_GET['section']) ? $_GET['section'] : '');
$ui_form = new iform($dbc, $os->auth);

$panel->setMeta(array(
	array("pmr", "ส่งเม็ดผลิตแท่ง 1kg.", "far fa-user"),
	array("recycle", "ส่ง PMR ผลิตเม็ดเงิน Recycle", "far fa-user"),
	array("out", "ตัดเพื่อเปลี่ยนประเภท Products", "far fa-user"),
	array("in", "เพิ่ม / ย้ายประเภท Products", "far fa-user"),

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
		'apps/production_pmr/include/interface.js',
		'apps/production_prepare/include/interface.js',
		'apps/production/include/interface.js',
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
			case "pmr":
				include "control/controller.pmr.view.js";
				include "control/controller.pmr.remove.js";
				include "control/controller.pmr.prepare.js";
				include "control/controller.pmr.add.js";
				include "control/controller.pmr.approve.js";
				break;
			case "recycle":
				include "control/controller.recycle.view.js";
				include "control/controller.recycle.remove.js";
				include "control/controller.recycle.add.js";
				include "control/controller.recycle.approve.js";
				break;
			case "in":
				include "control/controller.in.view.js";
				include "control/controller.in.add.js";
				include "control/controller.in.remove.js";
				include "control/controller.in.approve.js";
				break;
			case "out":
				include "control/controller.out.view.js";
				include "control/controller.out.add.js";
				include "control/controller.out.remove.js";
				include "control/controller.out.approve.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>