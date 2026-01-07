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
	
	class mypanel extends ipanel{
		protected $dashboard = array(
			array(
				array(
					"widget" => "diags_monitor",
					"setting" => "",
					"script" => "control/widget.start.js"
				)
			),
			array(
				array(
					"widget" => "cpu_load",
					"setting" => "",
					"script" => "control/widget.start.js"
				),
				array(
					"widget" => "dbsize",
					"setting" => "",
					"script" => "control/widget.start.js"
				),
				array(
					"widget" => "clock",
					"setting" => ""
				)
			),
			array(
				array(
					"widget" => "clock",
					"setting" => ""
				),array(
					"widget" => "clock",
					"setting" => ""
				)
			)
		);
		
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
			$path = "../../widget/".$app."/index.php";
			if(file_exists($path)){
				include $path;
			}else{
				echo 'No App Installed';
			}
			
		}	
		
		function EchoInterface(){
			$dbc = $this->dbc;
			echo '<div class="row">';
				echo '<div class="col-xl-12">';
				$l = 0;
				$script_roll = array();
				foreach($this->dashboard as $row){
					echo '<div class="row">';
					$article_class = $this->getArticleClass(count($row));
					$c = 0;
					foreach($row as $column){
						$id = "widget_".$l.$c;
						echo '<div class="'.$article_class.'">';
							$this->loadWidget($id,$column['widget'],$column['setting']);
						echo '</div>';
						if(isset($column['script'])){
							$script_path = "widget/".$column['widget']."/".$column['script'];
							if(!in_array($script_path,$script_roll)){
								array_push($script_roll,$script_path);
							}
						}
						$c++;
					}
					echo '';
					echo '</div>';
					$l++;
				}
				echo '</div>';
			echo '</div>';
			echo '<script src="apps/dashboard/include/interface.js"></script>';
			echo '<script>';
				foreach($script_roll as $script){
					include "../../".$script;
				}
			echo '</script>';
		}
	}
	
	$panel = new mypanel($dbc,$os->auth);
	$panel->setApp("dashboard","Dashbaord");

	$panel->EchoInterface();
	$dbc->Close();

/*
	$l = 0;
	$script_roll = array();
	foreach($dashboard as $row){
		echo '<div class="row">';
		$article_class = $me->getArticleClass(count($row));
		$c = 0;
		foreach($row as $column){
			$id = "widget_".$l.$c;
			echo '<article class="'.$article_class.'">';
				$me->loadWidget($id,$column['widget'],$column['setting']);
				
			echo '</article>';
			if(isset($column['script'])){
				$script_path = "widget/".$column['widget']."/".$column['script'];
				if(!in_array($script_path,$script_roll)){
					array_push($script_roll,$script_path);
				}
			}
			$c++;
		}
		echo '';
		echo '</div>';
		$l++;
	}
	*/
?>