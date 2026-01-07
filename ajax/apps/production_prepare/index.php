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

	$panel->setApp("production_prepare","Prepare");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'prepare');
	$panel->setSection(isset($_GET['section'])?$_GET['section']:'');
	$ui_form = new iform($dbc,$os->auth);
	
	$panel->setMeta(array(
		array("prepare","Prepare","far fa-user"),
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
		'apps/production_prepare/include/interface.js',
		'apps/production/include/interface.js',
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
			case "prepare":
				include "control/controller.prepare.view.js";
				if($os->allow("production_prepare","add"))include "control/controller.prepare.add.js";
				if($os->allow("production_prepare","edit"))include "control/controller.prepare.edit.js";
				if($os->allow("production_prepare","remove"))include "control/controller.prepare.remove.js";
				if($os->allow("production_prepare","remove"))include "control/controller.pack.remove.js";
				if($os->allow("production_prepare","approve"))include "control/controller.prepare.approve.js";
				if($os->allow("production_prepare","edit"))include "../production/control/controller.file.upload.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
