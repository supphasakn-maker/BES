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
	
	$panel->setApp("accctrl","Access Control");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'user');
	
	$panel->setMeta(array(
		array('account'	,$os->tr('main.account'),	'far fa-building'),
		array('group'	,$os->tr('main.group'),		'far fa-clone'),
		array('user'	,$os->tr('main.user'),		'far fa-user')
	));
	
?>

<?php
	$panel->PageBreadcrumb();
?>
<div class="row">
	<div class="col-xl-12">
	
	<?php
		$panel->EchoInterface();
		//$panel->EchoInterface_verticle();
	?>
	</div>
</div>
<script>
	var plugins = [
		'apps/accctrl/include/interface.js',
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
		echo "\n".'var lang = '.($os->get_lang_json()).";\n";
		switch($panel->getView()){
			case "account":
				include "control/controller.account.view.js";
				include "../contact/control/controller.contact.view.js";
				include "../contact/control/controller.address.js";
				include "../contact/control/controller.organization.lookup.js";
				if($os->allow("accctrl","add"))include "control/controller.account.add.js";
				if($os->allow("accctrl","edit"))include "control/controller.account.edit.js";
				if($os->allow("accctrl","remove"))include "control/controller.account.remove.js";
				
				break;
			case "group":
				include "control/controller.group.view.js";
				if($os->allow("accctrl","add"))include "control/controller.group.add.js";
				if($os->allow("accctrl","remove"))include "control/controller.group.remove.js";
				if($os->allow("accctrl","edit"))include "control/controller.group.edit.js";
				if($os->allow("accctrl","edit"))include "control/controller.group.permission.js";
				break;
			case "user":
				include "control/controller.user.view.js";
				include "../contact/control/controller.address.js";
				if($os->allow("accctrl","add"))include "control/controller.user.add.js";
				if($os->allow("accctrl","edit"))include "control/controller.user.edit.js";
				if($os->allow("contact","edit"))include "../engine/control/controller.file.upload.js";
				if($os->allow("accctrl","remove"))include "control/controller.user.remove.js";
				
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>
<?php
	$dbc->Close();
?>