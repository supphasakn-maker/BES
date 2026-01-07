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
	
	$panel->setApp("zystem","Zystem");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'core');
	$panel->setSection(isset($_GET['section'])?$_GET['section']:'overview');

	$panel->setMeta(array(
		array('core'		,"Core System",	'fa fa-wrench'),
		array('diagnostic'	,"Diagnostics",	'fa fa-clipboard-check'),
		array('engine'		,"Engine",		'far far-gravel'),
	));

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
			'apps/accctrl/include/interface.js',
			'apps/profile/include/interface.js',
			'apps/engine/include/interface.js',
			'apps/zystem/include/interface.js',
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
			case "core":
				switch($panel->getSection()){
					case "initial":
						include "control/core/controller.initial.js";
						break;
					case "license":
						include "control/core/controller.license.js";
						break;
					case "variable":
						include "control/core/controller.variable.js";
						break;
				}
				break;
			case "engine":
				switch($panel->getSection()){
					case "builder":
						include "control/engine/controller.builder.js";
						break;
					case "concurrent":
						include "control/engine/controller.concurrent.js";
						break;
				}
				break;
			
		}
		?>
	}).then(() => App.stopLoading())
</script>