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
	
	$panel->setApp("contact","Contact");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'organization');
	
	$panel->setMeta(array(
		array('organization'	,'Organization',	'far fa-building'),
		array('contact'			,'Contact',			'far fa-address-card')
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
			'apps/contact/include/interface.js',
			'apps/engine/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'
	];
	
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
		<?php
		switch($panel->getView()){
			case "contact":
				include "control/controller.contact.view.js";
				include "control/controller.address.js";
				if($os->allow("contact","add"))include "control/controller.contact.add.js";
				if($os->allow("contact","edit"))include "control/controller.contact.edit.js";
				if($os->allow("contact","edit"))include "control/controller.address.lookup.js";
				if($os->allow("contact","edit"))include "control/controller.address.dialog.js";
				if($os->allow("contact","edit"))include "../engine/control/controller.file.upload.js";
				if($os->allow("contact","remove"))include "control/controller.contact.remove.js";
				break;
			case "organization":
				include "control/controller.organization.view.js";
				include "control/controller.address.js";
				if($os->allow("contact","add"))include "control/controller.organization.add.js";
				if($os->allow("contact","edit"))include "control/controller.organization.edit.js";
				if($os->allow("contact","edit"))include "control/controller.address.lookup.js";
				if($os->allow("contact","edit"))include "control/controller.address.dialog.js";
				if($os->allow("contact","remove"))include "control/controller.organization.remove.js";
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>
<?php
	$dbc->Close();
?>