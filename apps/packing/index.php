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

	$panel->setApp("packing","Packing");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'packing');

	$panel->setMeta(array(
		array("packing","Packing","far fa-user"),
		array("repack","Repack","far fa-user"),
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
		'apps/packing/include/interface.js',
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
			case "packing":
				include "control/controller.packing.view.js";
				if($os->allow("packing","add"))include "control/controller.packing.add.js";
				if($os->allow("packing","edit"))include "control/controller.packing.edit.js";
				if($os->allow("packing","remove"))include "control/controller.packing.remove.js";
				if($os->allow("packing","edit"))include "control/controller.packing.submit.js";
				if($os->allow("packing","edit"))include "control/controller.packing.cancel.js";
				break;
			case "repack":
				include "control/controller.repack.view.js";
				if($os->allow("packing","split"))include "control/controller.repack.split.js";
				if($os->allow("packing","combine"))include "control/controller.repack.combine.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
