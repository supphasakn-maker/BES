<?php
class meClass extends widgeteer{
	private $app = "profile";
	private $view = "overview";
	private $dbc = null;
	
	function __construct($dbc=null){
		$this->dbc = $dbc;
	}
	
	private $header_meta = array(
		array('overview'	,"Overview",		'fa fa-lg fa-info'),
		array('message'		,"Message",			'fa fa-lg fa-envelope'),
		array('activity'	,"Activity",		'fa fa-lg fa-rss')
	);
	
	function setView($view){
		$this->view = $view;
	}
	function getView(){
		return $this->view;
	}
	
	function PageBreadcrumb(){
		echo '<h1 class="page-title txt-color-blueDark"> ';
		echo '<i class="fa-fw fa fa-home"></i> Home ';
		echo '<span> > Profile</span> ';
		foreach($this->header_meta as $header){
			if($header[0]==$this->view){
				echo '<span> > '.$header[1].'</span>';
			}
		}
		echo '</h1>';
	}
	
	function widgetHeader(){
		$dbc = $this->dbc;
		echo '<ul class="nav nav-tabs">';
		foreach($this->header_meta as $header){
			echo '<li'.($header[0]==$this->view?' class="active"':'').'>';
				echo '<a href="#apps/profile/index.php?view='.$header[0].'">';
					echo '<i class="'.$header[2].'"></i>';
					echo '<span class="hidden-mobile hidden-tablet"> '.$header[1].' </span>';
				echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}
				
	function widgetBody(){
		global $os;
		$dbc = $this->dbc;
		echo '<div class="tab-content">';
			echo '<div class="tab-pane fade in active" id="'.$this->app.'_'.$this->view.'">';
			switch($this->view){
				case "overview":
					include_once "view/page.overview.php";
					break;
				case "message":
					include_once "view/page.message.php";
					break;
			}
			echo '</div>';
		echo '</div>';
	}
}
?>