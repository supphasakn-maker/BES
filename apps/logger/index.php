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
	$os->initial_lang("lang");
	$panel = new ipanel($dbc,$os->auth);
	
	$panel->setApp("logger","Logger");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'log');
	
	$panel->setMeta(array(
		array('log'	,"Logs",			'far fa-history'),
		array('report'	,"Report",			'far fa-book'),
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
			'apps/logger/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/moment/moment.min.js'
	];
	
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
		<?php
		switch($panel->getView()){
			case "log":
				include "control/controller.log.view.js";
				include "control/controller.log.lookup.js";
				break;
			case "report":
				include "control/controller.report.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>
<?php
	$dbc->Close();
?>