<?php
	$aSection = array(
		array('overview'	,"Overview",	'fal fa-home'),
		array('checklist'	,"Checklist",	'fal fa-check'),
		array('physical'	,"Physical",	'fal fa-microchip'),
		array('software'	,"Software",	'fal fa-window'),
		array('showall'		,"All Theme",	'fal fa-window')
	);

?>

<div class="row">
	<div class="col-auto">
		<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
		<?php
		foreach($aSection as $sec){
			echo '<a class="nav-link'.($this->section==$sec[0]?" active":"").'" href="#apps/zystem/index.php?view=diagnostic&section='.$sec[0].'" aria-selected="true">';
				echo '<i class="'.$sec[2].'"></i>';
				echo '<span class="hidden-sm-down ml-1"> '.$sec[1].'</span>';
			echo '</a>';
		}
		?>
		</div>
	</div>
	<div class="col">
	<?php
		foreach($aSection as $sec){
			if($this->section==$sec[0]){
			echo '<div class="tab-content" id="v-pills-tabContent">';
				echo '<div class="tab-pane fade'.($this->section==$sec[0]?" show active":"").'" id="v-pills-'.$sec[0].'" role="tabpanel" aria-labelledby="v-pills-'.$sec[0].'-tab">';
					include "view/diagnostic/page.".$sec[0].".php";
				echo '</div>';
			echo '</div>';
			}
		}
	?>
	</div>
</div>