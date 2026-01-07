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

	$panel->setApp("delivery","Delivery");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'delivery');
	$panel->setSection(isset($_GET['section'])?$_GET['section']:'');

	$panel->setMeta(array(
		array("delivery","Delivery","far fa-user"),
		array("report","รายงานสินค้าคงคลัง","far fa-user"),
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
		'apps/delivery/include/interface.js',
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
				if($os->allow("delivery","edit"))include "control/controller.delivery.prepare.js";
				if($os->allow("delivery","edit"))include "control/controller.delivery.delivery.js";
				if($os->allow("delivery","lookup"))include "control/controller.delivery.lookup.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
