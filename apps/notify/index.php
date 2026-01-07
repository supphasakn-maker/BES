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
	
	$panel->setApp("notify","Notification");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'message');
	
	$panel->setMeta(array(
		array('notification',"Notification",	'far fa-bell'),
		array('message'		,"Message",			'far fa-envelope')
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
			'apps/notify/include/interface.js',
			'apps/contact/include/interface.js',
			'apps/engine/include/interface.js',
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
		case "message":
				include "control/controller.message.view.js";
				include "control/controller.message.add.js";
				include "control/controller.message.edit.js";
				include "control/controller.message.remove.js";
				include "control/controller.message.lookup.js";
				break;
			case "notification":
				include "control/controller.notification.view.js";
				include "control/controller.notification.add.js";
				include "control/controller.notification.edit.js";
				include "control/controller.notification.remove.js";
				include "control/controller.notification.lookup.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>
<?php
	$dbc->Close();
?>