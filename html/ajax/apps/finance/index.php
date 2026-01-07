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

	$panel->setApp("finance","Financial");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'payment');

	$panel->setMeta(array(
		array("payment","Payment","far fa-user"),
		array("deposit","Deposit","far fa-user"),
		array("overview","Overview","far fa-user"),
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
		'apps/finance/include/interface.js',
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
			case "payment":
				include "control/controller.payment.view.js";
				if($os->allow("finance","add"))include "control/controller.payment.add.js";
				if($os->allow("finance","edit"))include "control/controller.payment.edit.js";
				if($os->allow("finance","remove"))include "control/controller.payment.remove.js";
				if($os->allow("finance","edit"))include "control/controller.payment.mapping.js";
				if($os->allow("finance","approve"))include "control/controller.payment.approve.js";
				break;
			case "prepare":
				include "control/controller.prepare.view.js";
				if($os->allow("finance","payment"))include "control/controller.prepare.payment.js";
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
