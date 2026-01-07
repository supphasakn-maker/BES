<?php
	session_start();
	@ini_set('display_errors',1);
	include "../../config/define.php";
	include "../../include/db.php";
	include "../../include/oceanos.php";
	include "../../include/iface.php";
	include "../../include/demo.php";
	$demo = new demo;
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	$panel = new ipanel($dbc,$os->auth);

	$panel->setApp("pack_prepare","Pack Prepare");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'prepare');
?>
<div class="card">
	<div class="card-body">
		<?php include "../sales/view/page.packing.php"; ?>
	</div>
</div>

	<script>
		var plugins = [
			'apps/sales/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'plugins/moment/moment.min.js'
		];
		App.loadPlugins(plugins, null).then(() => {
			App.checkAll();
			
			<?php
				include "../sales/control/controller.packing.view.js";
				if($os->allow("pack_prepare","add"))include "../sales/control/controller.packing.add.js";
				if($os->allow("pack_prepare","edit"))include "../sales/control/controller.packing.edit.js";
				if($os->allow("pack_prepare","remove"))include "../sales/control/controller.packing.remove.js";
				if($os->allow("pack_prepare","approve"))include "../sales/control/controller.packing.approve.js";
			?>
			
			
		}).then(() => App.stopLoading())
	</script>