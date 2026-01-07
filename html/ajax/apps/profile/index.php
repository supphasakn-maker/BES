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
	
	$panel->setApp("profile","Profile");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'overview');
	
	$panel->setMeta(array(
		array('overview'	,"Information",	'far fa-lg fa-info'),
		array('message'		,"Message",		'far fa-lg fa-envelope'),
		array('activity'	,"Activity",	'far fa-lg fa-rss')
	));
	
	$panel->EchoInterface_verticle();
	
?>
      

      <script>
        var plugins = [
			'apps/profile/include/interface.js',
			'apps/contact/include/interface.js',
			'apps/accctrl/include/interface.js',
			'apps/engine/include/interface.js',
			'plugins/autosize/autosize.min.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'plugins/moment/moment.min.js'
        ]
        App.loadPlugins(plugins, null).then(() => {
          autosize(document.querySelectorAll('textarea.autosize'))
		<?php
		switch($panel->getView()){
			case "overview":
				include_once "../contact/control/controller.address.js";
				include_once "../accctrl/control/controller.user.edit.js";
				include_once "control/controller.profile.setting.js";
				include_once "control/controller.profile.quote.js";
				include "../engine/control/controller.file.upload.js";
				include_once "control/controller.sendmail.js";
				break;
			case "message":
				include_once "control/controller.message.view.js";
				//include_once "control/controller.profile.avatar.js";
				//include_once "control/controller.profile.qoute.js";
				break;
		}
		?>
        }).then(() => App.stopLoading())
      </script>
