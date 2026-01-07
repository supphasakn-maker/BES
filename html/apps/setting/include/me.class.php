<?php
class meClass extends widgeteer{
	private $dbc = null;
	private $os = null;
	private $app = "setting";
	private $view = "overview";
	private $section = "system";
	
	
	private $header_meta = null;
	
	function __construct($dbc,$os){
		$this->dbc = $dbc;
		$this->os = $os;
		
		$this->header_meta = array(
			array('overview'	,$this->os->tr("general.overview"),	'fa fa-group'),
			array('profile'		,$this->os->tr("general.my_profile"),	'fa fa-user'),
			array('company'		,$this->os->tr("general.my_company"),	'fa fa-user'),
			array('system'		,$this->os->tr("general.system"),		'fa fa-wrench')
		);
	}
	
	
	private $aSettingMenu = array(
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
	
	function setView($view){
		$this->view = $view;
	}
	function getView(){
		return $this->view;
	}
	function setSection($section){
		$this->section = $section;
	}
	function getSection(){
		return $this->section;
	}
	
	function PageBreadcrumb(){
		echo '<h1 class="page-title txt-color-blueDark"> ';
		echo '<i class="fa-fw fa fa-home"></i> '.$this->os->tr("general.home").' ';
		echo '<span> > '.$this->os->tr("general.setting").'</span> ';
		foreach($this->header_meta as $header){
			if($header[0]==$this->view){
				echo '<span> > '.$header[1].'</span>';
			}
		}
		echo '</h1>';
	}
	
	function widgetHeader(){
		echo '<ul class="nav nav-tabs">';
		foreach($this->header_meta as $header){
			echo '<li'.($header[0]==$this->view?' class="active"':'').'>';
				echo '<a href="#apps/setting/index.php?view='.$header[0].'">';
					echo '<i class="'.$header[2].'"></i>';
					echo '<span class="hidden-mobile hidden-tablet"> '.$header[1].' </span>';
				echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}
	
	function widgetBody(){
		$dbc=$this->dbc;
		$os=$this->os;
		echo '<div class="tab-content">';
			echo '<div class="tab-pane fade in active" id="'.$this->app.'_'.$this->view.'">';
			switch($this->view){
				case "overview":
					include_once "view/page.overview.php";
					break;
				case "profile":
					include_once "../profile/view/page.overview.php";
					break;
				case "company":
					include_once "view/page.company.php";
					break;
				case "system":
					echo '<div class="widget-body-toolbar">';
						echo '<div class="row">';
							echo '<div class="col-sm-6">';
								echo '<select class="form-control" onChange="fn.navigate(\'setting\',\'view=system&section=\'+$(this).val())">';
								foreach($this->aSettingMenu as $menu){
									if($menu['name']==$this->getSection()){
										$selected = " selected";
									}else{
										$selected = "";
									}
									echo '<option value="'.$menu['name'].'"'.$selected.'>'.$menu['caption'].'</option>';
								}
								echo '</select>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="padding-10">';
					foreach($this->aSettingMenu as $menu){
						if($menu['name']==$this->getSection()){
							include $menu['view'];
						}
					}
					echo '</div>';
					break;
			}
			echo '</div>';
		echo '</div>';
	}
	
	function loadSettingScript(){
		foreach($this->aSettingMenu as $menu){
			if($menu['name']==$this->getSection()){
				include $menu['control'];
			}
		}
		
	}
	
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	function getOS() {
		$os_platform  = "Unknown OS Platform";
		$os_array     = array(
							  '/windows nt 10/i'      =>  'Windows 10',
							  '/windows nt 6.3/i'     =>  'Windows 8.1',
							  '/windows nt 6.2/i'     =>  'Windows 8',
							  '/windows nt 6.1/i'     =>  'Windows 7',
							  '/windows nt 6.0/i'     =>  'Windows Vista',
							  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
							  '/windows nt 5.1/i'     =>  'Windows XP',
							  '/windows xp/i'         =>  'Windows XP',
							  '/windows nt 5.0/i'     =>  'Windows 2000',
							  '/windows me/i'         =>  'Windows ME',
							  '/win98/i'              =>  'Windows 98',
							  '/win95/i'              =>  'Windows 95',
							  '/win16/i'              =>  'Windows 3.11',
							  '/macintosh|mac os x/i' =>  'Mac OS X',
							  '/mac_powerpc/i'        =>  'Mac OS 9',
							  '/linux/i'              =>  'Linux',
							  '/ubuntu/i'             =>  'Ubuntu',
							  '/iphone/i'             =>  'iPhone',
							  '/ipod/i'               =>  'iPod',
							  '/ipad/i'               =>  'iPad',
							  '/android/i'            =>  'Android',
							  '/blackberry/i'         =>  'BlackBerry',
							  '/webos/i'              =>  'Mobile'
						);

		foreach ($os_array as $regex => $value)
			if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
				$os_platform = $value;
		return $os_platform;
	}

	function getBrowser() {
		$browser        = "Unknown Browser";

		$browser_array = array(
								'/msie/i'      => 'Internet Explorer',
								'/firefox/i'   => 'Firefox',
								'/safari/i'    => 'Safari',
								'/chrome/i'    => 'Chrome',
								'/edge/i'      => 'Edge',
								'/opera/i'     => 'Opera',
								'/netscape/i'  => 'Netscape',
								'/maxthon/i'   => 'Maxthon',
								'/konqueror/i' => 'Konqueror',
								'/mobile/i'    => 'Handheld Browser'
						 );

		foreach ($browser_array as $regex => $value)
			if (preg_match($regex, $_SERVER['HTTP_USER_AGENT']))
				$browser = $value;
		return $browser;
	}
	
	
}
?>