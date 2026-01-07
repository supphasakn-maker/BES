<?php
	global $_GET,$aSettingMenu ;
	$section = isset($_GET['section'])?$_GET['section']:"system";
	
	/*
	$rst = $this->dbc->GetRecord("apps","*");
	while($line = $this->dbc->Fetch($rst)){
		
	}
	*/
	
	echo '<div class="row">';
		echo '<div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">';
			echo '<select class="form-control" onChange="fn.navigate(\'setting\',\'view=system&section=\'+$(this).val())">';
			foreach($aSettingMenu as $menu){
				if($menu['name']==$section){
					$selected = " selected";
				}else{
					$selected = "";
				}
				echo '<option value="'.$menu['name'].'"'.$selected.'>'.$menu['caption'].'</option>';
			}
			echo '</select>';
		echo '</div>';
	echo '</div>';
	echo '<br>';
	foreach($aSettingMenu as $menu){
		if($menu['name']==$section){
			include $menu['view'];
		}
	}
?>


