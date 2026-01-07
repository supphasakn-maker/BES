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
	
	$panel->setApp("setting","Setting");
	$panel->setView(isset($_GET['view'])?$_GET['view']:'overview');
	$section = isset($_GET['section'])?$_GET['section']:"system";
	
	$panel->setMeta(array(
		array('overview'	,$os->tr("general.overview"),	'fa fa-group'),
		array('profile'		,$os->tr("general.my_profile"),	'fa fa-user'),
		array('company'		,$os->tr("general.my_company"),	'fa fa-user'),
		array('system'		,$os->tr("general.system"),		'fa fa-wrench')
	));
	
	$aSettingMenu = array(
		array(
			"name" => "system",
			"caption" => "General System Setting",
			"view" => "../../apps/setting/setting/view/page.setting.php",
			"control" => "../../apps/setting/setting/control/controller.general.js"
		),
		array(
			"name" => "menu",
			"caption" => "Initial Menu Setting",
			"view" => "../../apps/setting/setting/view/page.menu.php",
			"control" => "../../apps/setting/setting/control/controller.menu.js"
		)
	);
	
	
	$d = dir("../");
	while (false !== ($entry = $d->read())) {
		if($entry != "." || $entry != ".."){
			$file = "../".$entry."/setting/control.json";
			if(file_exists($file)){
				array_push($aSettingMenu ,json_decode(file_get_contents($file),true));
			}
		}
	}
	$d->close();
	
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
			'apps/accctrl/include/interface.js',
			'apps/profile/include/interface.js',
			'apps/engine/include/interface.js',
			'apps/setting/include/interface.js',
			'plugins/datatables/dataTables.bootstrap4.min.css',
			'plugins/datatables/responsive.bootstrap4.min.css',
			'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js',
			'plugins/select2/css/select2.min.css',
			'plugins/select2/js/select2.min.js',
			'plugins/jquery-ui-1.12.1.custom/jquery-ui.js',
			'plugins/moment/moment.min.js',
			'//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css',
			'//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js'
	];
	
	
	App.loadPlugins(plugins, null).then(() => {
		App.checkAll()
		<?php
		switch($panel->getView()){
			
			case "overview":
				//if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.edit.js";
				//if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.avatar.js";
				//if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.qoute.js";
				break;
			case "profile":
				if($os->allow("profile","edit"))include_once "../accctrl/control/controller.user.edit.js";
				if($os->allow("profile","edit"))include_once "../accctrl/control/controller.address.js";
				if($os->allow("profile","edit"))include_once "../profile/control/controller.profile.setting.js";
				if($os->allow("profile","edit"))include_once "../profile/control/controller.profile.quote.js";
				if($os->allow("profile","edit"))include_once "../engine/control/controller.file.upload.js";
				break;
				
			case "company":
				include "control/controller.company.edit.js";
				break;
			case "system":
				foreach($aSettingMenu as $menu){
					if($menu['name']==$section){
						include $menu['control'];
					}
				}
				//$me->loadSettingScript();
				break;
		}
		?>
	}).then(() => App.stopLoading())
</script>