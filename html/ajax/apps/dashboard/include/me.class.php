<?php
class meClass extends widgeteer{
	private $app = "dashbaord";
	private $view = "overview";
	private $dbc = null;
	
	function __construct($dbc=null){
		$this->dbc = $dbc;
	}
	
	function setView($view){
		$this->view = $view;
	}
	function getView(){
		return $this->view;
	}
	
	function getArticleClass($num){
		switch($num){
			case 1:
				return "col-sm-12 col-md-12 col-lg-12";
				break;
			case 2:
				return "col-sm-12 col-md-12 col-lg-6";
				break;
			case 3:
				return "col-sm-12 col-md-12 col-lg-4";
				break;
			case 4:
				return "col-sm-12 col-md-12 col-lg-3";
				break;
			case 5:
				return "col-sm-12 col-md-12 col-lg-2";
				break;
		}
		
	}
	
	function loadWidget($id,$app,$setting){
		$dbc = $this->dbc;
		if(file_exists("../../widget/".$app."/index.php")){
			include "../../widget/".$app."/index.php";
		}else{
			echo '';
		}
		
	}	
	
	function PageBreadcrumb(){
		echo '<h1 class="page-title txt-color-blueDark"> ';
		echo '<i class="fa-fw fa fa-home"></i> Home ';
		foreach($this->header_meta as $header){
			if($header[0]==$this->view){
				echo '<span> > '.$header[1].'</span>';
			}
		}
		echo '</h1>';
	}
}
?>