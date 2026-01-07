<?php

/*
 * 2020-10-17 : Created New System : Todsaporn S.
 * 
 */
 
class nebulaos extends oceanos{
	
	function load_topmenu($menu){
		echo '<nav class="main-navbar navbar main-navbar navbar-expand navbar-light shadow-sm sticky-top">';
			echo '<a class="nav-link nav-link-faded nav-icon rounded-circle" data-scroll="left" href="#"><i class="material-icons">chevron_left</i></a>';
			echo '<div class="navbar-collapse">';
				echo '<div class="navbar-nav nav-pills nav-gap-x-1">';
				foreach($menu as $menu_item){
					$has_submenu = isset($menu_item['submenu']);
					
					if($has_submenu)echo '<div class="nav-item dropdown">';
					$a_class = $has_submenu?"nav-link nav-link-faded dropdown-toggle has-icon":"nav-item nav-link nav-link-faded has-icon";
					$a_attr = $has_submenu?' role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference=".main"':"";
				
					echo '<a class="'.$a_class.'" href="#'.(isset($menu_item['path'])?$menu_item['path']:"").'"'.$a_attr.'>';
					$this->menu_load_icon($menu_item);
					echo $menu_item['name'];
					echo '</a>';
					if($has_submenu){
						echo '<div class="dropdown-menu">';
						foreach($menu_item['submenu'] as $sub_item){
							echo '<a class="dropdown-item has-icon" href="#'.(isset($sub_item['path'])?$sub_item['path']:"").'">';
							$this->menu_load_icon($sub_item);
							echo '<span class="mr-2"></span>';
							echo $sub_item['name'];
							echo '</a>';
						}
						echo '</div>';
					}
					if($has_submenu)echo '</div>';
				}
				echo '</div>';
			echo '</div>';
			echo '<a class="nav-link nav-link-faded nav-icon rounded-circle" data-scroll="right" href="#"><i class="material-icons">chevron_right</i></a>';
		echo '</nav>';
	}
	
	function load_menu($menu){
		foreach($menu as $menu_item){
			if($this->allow($menu_item['appname'],"view")){
				echo '<li class="nav-item">';
					echo '<a class="nav-link has-icon'.(isset($menu_item['submenu'])?" treeview-toggle":"").'" href="#'.(isset($menu_item['path'])?$menu_item['path']:"").'">';
					$this->menu_load_icon($menu_item);
					echo $menu_item['name'];
					echo '</a>';
					
					if(isset($menu_item['submenu'])){
						echo '<ul class="nav">';
						foreach($menu_item['submenu'] as $sub_item){
							if($this->allow($sub_item['appname'],"view")){
								echo '<li class="nav-item">';
								echo '<a class="nav-link has-icon" href="#'.(isset($sub_item['path'])?$sub_item['path']:"").'">';
								$this->menu_load_icon($sub_item);
								echo $sub_item['name'];
								echo '</a>';
								echo '</li>';
							}
						}
						echo '</ul>';
					}
				echo '</li>';
			}
		}
	}
	
	function menu_load_icon($item){
		 //none,font-awesome,material,feather
		$icon_type = isset($item['icon'])?(isset($item['icon_type'])?$item['icon_type']:"font-awesome"):"none";
		switch($icon_type){
			case "font-awesome":
				echo '<i class="'.$item['icon'].'"></i>';
				break;
			case "material":
				echo '<i class="material-icons mr-1">'.$item['icon'].'</i>';
				break;
			case "feather":
				echo '<i data-feather="'.$item['icon'].'"></i>';
				break;
			
		}
	}
	
	function create_table_from_record($data,$class="table"){
		echo '<table class="'.$class.'">';
			echo '<tbody>';
				foreach($data as $key => $item){
					if(!is_numeric($key)){
						echo '<tr>';
							echo '<th>'.$key.'</th>';
							echo '<td>'.$item.'</td>';
						echo '</tr>';
					}
				}
			echo '</tbody>';
		echo '<table>';
	}
	
	
	
}

?>