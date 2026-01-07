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

	$panel->setApp("repack","Repack");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'repack');

	$panel->setMeta(array(
		array("repack","Repack","far fa-user"),
		array("alldata","All Data","far fa-user"),
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
		'apps/repack/include/interface.js',
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
			case "alldata":
				include "control/controller.alldata.view.js";
			case "repack":
				include "control/controller.repack.view.js";
				if($os->allow("repack","edit"))include "control/controller.repack.combine.js";
				if($os->allow("repack","edit"))include "control/controller.repack.split.js";
				if($os->allow("repack","remove"))include "control/controller.repack.restore.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
