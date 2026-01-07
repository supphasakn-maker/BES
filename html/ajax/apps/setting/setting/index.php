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
			"view" => "../../apps/setting/view/page.system.setting.php",
			"control" => "../../apps/setting/control/controller.system.general.js"
		),
		array(
			"name" => "menu",
			"caption" => "Initial Menu Setting",
			"view" => "../../apps/setting/view/page.system.menu.php",
			"control" => "../../apps/setting/control/controller.system.menu.js"
		),
		array(
			"name" => "auth",
			"caption" => "Authentication Setting",
			"view" => "../../apps/setting/view/page.system.auth.php",
			"control" => "../../apps/setting/control/controller.system.auth.js"
		)
	);
	
?>
<script src="apps/accctrl/include/interface.js"></script>
<script src="apps/profile/include/interface.js"></script>
<script src="apps/engine/include/interface.js"></script>
<script src="apps/setting/include/interface.js"></script>
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
	loadScript("js/datagrid/datatables/datatables.bundle.js", function(){
	loadScript("js/datagrid/datatables/datatables.export.js", function(){
	loadScript("js/dependency/moment/moment.js",function(){
	<?php
		switch($panel->getView()){
			case "overview":
				if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.edit.js";
				if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.avatar.js";
				if($os->allow("setting","edit"))include_once "../profile/control/controller.profile.qoute.js";
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
					foreach($aSettingMenu as $menu){
						if($menu['name']==$section){
							include $menu['control'];
						}
					}
				}
				//$me->loadSettingScript();
				break;
		}
	?>
	});});});
</script>