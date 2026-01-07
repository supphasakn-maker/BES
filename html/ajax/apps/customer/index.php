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

	$panel->setApp("customer","Customer");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'customer');

	$panel->setMeta(array(
		array("group","Group","far fa-user"),
		array("customer","Customer","far fa-user"),
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
		'apps/customer/include/interface.js',
		'plugins/datatables/dataTables.bootstrap4.min.css',
		'plugins/datatables/responsive.bootstrap4.min.css',
		'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
		'plugins/select2/css/select2.min.css',
		'plugins/select2/js/select2.min.js',
		'plugins/moment/moment.min.js',
		'plugins/jquery-ui-1.12.1.custom/jquery-ui.js'
	];
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
	<?php
		switch($panel->getView()){
			case "group":
				include "control/controller.group.view.js";
				if($os->allow("customer","remove"))include "control/controller.group.remove.js";
				if($os->allow("customer","add"))include "control/controller.group.add.js";
				if($os->allow("customer","edit"))include "control/controller.group.edit.js";
				break;
			case "customer":
				include "control/controller.customer.view.js";
				if($os->allow("customer","remove"))include "control/controller.customer.remove.js";
				if($os->allow("customer","add"))include "control/controller.customer.add.js";
				if($os->allow("customer","edit"))include "control/controller.customer.edit.js";
				if($os->allow("customer","edit"))include "control/controller.customer.upload.js";
				break;
		}
	?>
	}).then(() => App.stopLoading())
</script>
