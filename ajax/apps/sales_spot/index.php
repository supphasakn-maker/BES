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
	$ui_form = new iform($dbc,$os->auth);

	$panel->setApp("sales_spot","Sales Spot");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'spot');

	$panel->setMeta(array(
		array("spot","Spot","far fa-user"),
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
		'apps/sales_spot/include/interface.js',
		'apps/sales/include/interface.js',
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
			case "spot":
				include "control/controller.spot.view.js";
				if($os->allow("sales","add"))include "../sales/control/controller.spot.add.js";
				if($os->allow("sales","edit"))include "../sales/control/controller.spot.edit.js";
				if($os->allow("sales","remove"))include "../sales/control/controller.spot.remove.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
