<?php
	
	$this->setSection(isset($_GET['section'])?$_GET['section']:'country');
	$aSection = array(
		array('country'		,"Country",		'fa fa-lg fa-globe'),
		array('city'		,"City",		'fa fa-lg fa-building'),
		array('district'	,"District",	'fa fa-lg fa-train'),
		array('subdistrict'	,"Subdistrict",	'fa fa-lg fa-road')
	);
	
?>
<div class="row gutters-sm">
	<div class="col-6 col-md-2 col-sm-3">
		<nav class="nav nav-gap-y-1 flex-column">
		<?php
		foreach($aSection as $sec){
			echo '<a class="nav-item nav-link nav-link-faded has-icon'.($this->section==$sec[0]?" active":"").'" href="#apps/database/index.php?view=geography&section='.$sec[0].'">';
				echo '<i class="'.$sec[2].'"></i>';
				echo '<span class="hidden-sm-down ml-1"> '.$sec[1].'</span>';
			echo '</a>';
		}
		?>
		</nav>
	</div>
	<div class="col-6  col-md-10 col-sm-9">
	<?php
		foreach($aSection as $sec){
			if($this->section==$sec[0]){
			echo '<div class="tab-content">';
				echo '<div class="tab-pane fade'.($this->section==$sec[0]?" show active":"").'">';
					include "view/geography/page.".$sec[0].".php";
				echo '</div>';
			echo '</div>';
			}
		}
	?>
	</div>
</div>


