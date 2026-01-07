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

	$panel->setApp("production_crucible","Crucible");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'crucible');
	$panel->setSection(isset($_GET['section'])?$_GET['section']:'');
	$ui_form = new iform($dbc,$os->auth);
	
	$panel->setMeta(array(
		array("crucible","Crucible","far fa-user"),
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
		'apps/production_crucible/include/interface.js',
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
			case "crucible":
				include "control/controller.crucible.view.js";
				include "control/controller.crucible.approve.js";
				include "control/controller.crucible.add.js";
				include "control/controller.crucible.remove.js";
				if($os->allow("production_crucible","edit"))include "control/controller.crucible.lookup.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
