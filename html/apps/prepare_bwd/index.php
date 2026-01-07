<?php
	session_start();
	@ini_set('display_errors',1);
	include "../../config/define.php";
	include "../../include/db.php";
	include "../../include/oceanos.php";
	include "../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	$panel = new ipanel($dbc,$os->auth);

	$panel->setApp("prepare_bwd","Prepare Delivery");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'delivery');
	$panel->setSection(isset($_GET['section'])?$_GET['section']:'');
	$ui_form = new iform($dbc,$os->auth);

	$panel->setMeta(array(
		array("delivery","Bowins Design Delivery","far fa-user"),
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
		'apps/prepare_bwd/include/interface.js',
		'apps/sales_bwd/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/select2/js/select2.min.js',
		'plugins/moment/moment.min.js'
	];
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
	<?php
		switch($panel->getView()){
			case "delivery":
				include "control/controller.delivery.view.js";
				
				if($os->allow("prepare_bwd","remove"))include "../sales_bwd/control/controller.delivery.remove.js";
				if($os->allow("prepare_bwd","edit"))include "control/controller.delivery.packing.js";

				if($os->allow("prepare_bwd","edit"))include "../sales_bwd/control/controller.delivery.billing.js";
				if($os->allow("prepare_bwd","edit"))include "../sales_bwd/control/controller.delivery.payment.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
