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

	$panel->setApp("trade_spot","Trade Spot");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'trading');

	$panel->setMeta(array(
		array("trading","Trading","far fa-user"),
		
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
		'apps/trade_spot/include/interface.js',
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
			case "trading":
				include "control/controller.trading.view.js";
				if($os->allow("trade_spot","add"))include "control/controller.trading.add.js";
				if($os->allow("trade_spot","edit"))include "control/controller.trading.edit.js";
				if($os->allow("trade_spot","remove"))include "control/controller.trading.remove.js";
				break;
			case "history":
				include "control/controller.history.view.js";
				if($os->allow("trade_spot","add"))include "control/controller.history.add.js";
				if($os->allow("trade_spot","edit"))include "control/controller.history.edit.js";
				if($os->allow("trade_spot","remove"))include "control/controller.history.remove.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
