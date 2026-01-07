<?php
/*
 * 2017-04-11 : Initial Widget Modules : Todsaporn S.
 * 
 */
class widgeteer{
	private $wid = 0;
	private $colorbutton = ' data-widget-colorbutton="false"';	
	private $editbutton = ' data-widget-editbutton="false"';	
	private $togglebutton = ' data-widget-togglebutton="false"';
	private $deletebutton = ' data-widget-deletebutton="false"';	
	private $fullscreenbutton = ' data-widget-fullscreenbutton="false"';	
	private $custombutton = ' data-widget-custombutton="false"';
	private $collapsed = ' data-widget-collapsed="false"';	
	private $sortable = ' data-widget-sortable="false"';
	
	function widgetHeader(){
		echo '<h2><strong>Default</strong> <i>Widget</i></h2>';
	}
	
	function widgetBody(){
		
	}
	
	function widgetEditbox(){
		
	}
	
	function widget_option($param){
		$option = explode(",",$param);
		foreach($option as $opt){
			switch($opt){
				case "colorbutton":$this->colorbutton = ' data-widget-colorbutton="true"';break;
				case "editbutton":$this->editbutton = ' data-widget-editbutton="true"';break;
				case "togglebutton":$this->togglebutton = ' data-widget-togglebutton="true"';break;
				case "deletebutton":$this->deletebutton = ' data-widget-deletebutton="true"';break;
				case "fullscreenbutton":$this->fullscreenbutton = ' data-widget-fullscreenbutton="true"';break;
				case "custombutton":$this->custombutton = ' data-widget-custombutton="true"';break;
				case "collapsed":$this->collapsed = ' data-widget-collapsed="true"';break;
				case "sortable":$this->sortable = ' data-widget-sortable="true"';break;
			} 
		} 
	}
	
	
	
	function widget(){
		$this->wid++;
		$id = "wid-id-".$this->wid;
		$class = "jarviswidget";
		$div_begin = '<div';
			$div_begin .= ' id="'.$id.'"';
			$div_begin .= ' class="'.$class.'"';
			$div_begin .= $this->colorbutton;
			$div_begin .= $this->editbutton;
			$div_begin .= $this->togglebutton;
			$div_begin .= $this->deletebutton;
			$div_begin .= $this->fullscreenbutton;
			$div_begin .= $this->custombutton;
			$div_begin .= $this->collapsed;
			$div_begin .= $this->sortable;
		$div_begin .= '>';
		echo $div_begin;
			echo '<header>';		
				$this->widgetHeader();
			echo '</header>';
			echo '<div>';
				echo '<div class="jarviswidget-editbox">';
					$this->widgetEditbox();
				echo '</div>';
				echo '<div class="widget-body no-padding">';
					$this->widgetBody();
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
	}
	

}
?>