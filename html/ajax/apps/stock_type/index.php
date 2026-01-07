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

	$panel->setApp("stock_type","Stock Type");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'data');

	$panel->setMeta(array(
		array("data","Data","far fa-user"),
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
		'apps/stock_type/include/interface.js',
		'apps/purchase/include/interface.js',
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
			case "data":
				include "../purchase/control/controller.spot.view.js";
				if($os->allow("stock_type","add"))include "../purchase/control/controller.spot.add.js";
				if($os->allow("stock_type","edit"))include "../purchase/control/controller.spot.edit.js";
				if($os->allow("stock_type","remove"))include "../purchase/control/controller.spot.remove.js";
				if($os->allow("stock_type","edit"))include "../purchase/control/controller.spot.split.js";
				if($os->allow("stock_type","edit"))include "../purchase/control/controller.spot.combine.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
