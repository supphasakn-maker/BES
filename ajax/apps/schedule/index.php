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

	$panel->setApp("schedule","Schedule");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'order');

	$panel->setMeta(array(
		array("order","Order","far fa-user"),
	));
	$panel->PageBreadcrumb();
	
	
	if($panel->getView()=="printable"){
		include "view/page.order.view.php";
		
	}else{	
		echo '<div class="row">';
			echo '<div class="col-xl-12">';
				$panel->EchoInterface();
			echo '</div>';
		echo '</div>';
	}
?>
<script>

	var plugins = [
		'apps/schedule/include/interface.js',
		'apps/customer/include/interface.js',
		'apps/sales/include/interface.js',
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
			case "order":
				include "control/controller.order.view.js";
				//if($os->allow("schedule","remove"))include "control/controller.order.remove.js";
				//if($os->allow("schedule","add"))include "control/controller.order.add.js";
				//if($os->allow("schedule","edit"))include "control/controller.order.edit.js";
				//if($os->allow("schedule","edit"))include "control/controller.order.split.js";
				//if($os->allow("schedule","edit"))include "control/controller.order.postpone.js";
				
				//include "../sales/control/controller.order.view.js";
				
				if($os->allow("schedule","add"))include "../sales/control/controller.order.add.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.edit.js";
				if($os->allow("schedule","remove"))include "../sales/control/controller.order.remove.js";
				if($os->allow("schedule","remove"))include "../sales/control/controller.order.remove_each.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.split.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.postpone.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.lock.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.print.js";
				if($os->allow("schedule","edit"))include "../sales/control/controller.order.add_delivery.js";
				
				break;
}
	?>
	}).then(() => App.stopLoading())
</script>
