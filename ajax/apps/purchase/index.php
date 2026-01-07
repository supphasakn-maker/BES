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
	$panel->setApp("purchase","Purchase");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'spot');

	$panel->setMeta(array(
		array("spot","SPOT","far fa-user"),
		array("usd","USD","far fa-user"),
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
				if($os->allow("purchase","add"))include "control/controller.spot.add.js";
				if($os->allow("purchase","edit"))include "control/controller.spot.edit.js";
				if($os->allow("purchase","edit"))include "control/controller.spot.remove.js";
				if($os->allow("purchase","edit"))include "control/controller.spot.split.js";
				if($os->allow("purchase","edit"))include "control/controller.spot.combine.js";
				break;
			case "usd":
				include "control/controller.usd.view.js";
				if($os->allow("purchase","add"))include "control/controller.usd.add.js";
				if($os->allow("purchase","edit"))include "control/controller.usd.edit.js";
				if($os->allow("purchase","remove"))include "control/controller.usd.remove.js";
				if($os->allow("purchase","split"))include "control/controller.usd.split.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
